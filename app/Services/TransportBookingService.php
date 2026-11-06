<?php

namespace App\Services;

use App\Models\SalesInvoice;
use App\Models\TransportBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransportBookingService
{
    public function generateBookingsForInvoice(SalesInvoice $invoice, array $customBoxQuantities = [])
    {
        $totalQty = (float)$invoice->items->sum('quantity');
        
        $customer = $invoice->customer;

        if ($customer && (
            stripos($customer->name, 'CASH') !== false ||
            stripos($customer->name, 'ARUMUGAM') !== false ||
            stripos($customer->name, 'SRIVARSAN') !== false
        )) {
            TransportBooking::where('sales_invoice_id', $invoice->id)->delete();
            return collect([]);
        }

        $preferredTransport = $invoice->transporter_name ?: ($customer->transport_name ?? 'None');
        $bookingOffice = (!empty($customer->booking_office) && $customer->booking_office !== 'None')
            ? $customer->booking_office
            : ($customer && $customer->city ? $customer->city->city_name : ($customer && $customer->place ? $customer->place->place_name : ''));
        $standardRate = (float)($customer->transport_rate ?? 0);

        $existing = TransportBooking::where('sales_invoice_id', $invoice->id)->orderBy('id', 'asc')->get();
        $bookedWithLr = $existing->whereNotNull('lr_no')->where('lr_no', '!=', '')->first();
        $primaryBooking = $bookedWithLr ?: $existing->first();

        if ($existing->count() > 1) {
            $keepId = $primaryBooking ? $primaryBooking->id : null;
            TransportBooking::where('sales_invoice_id', $invoice->id)
                ->when($keepId, function ($q) use ($keepId) {
                    $q->where('id', '!=', $keepId);
                })
                ->delete();
        }

        $createdAt = $invoice->created_at ?: now();
        $updatedAt = $invoice->updated_at ?: now();

        $bookingDate = $invoice->tran_doc_date ? Carbon::parse($invoice->tran_doc_date) : null;

        if ($primaryBooking) {
            $primaryBooking->customer_id = $customer ? $customer->id : null;
            $primaryBooking->quantity = $totalQty;
            $primaryBooking->preferred_transport = $preferredTransport;
            $primaryBooking->booking_office = $bookingOffice;
            $primaryBooking->booking_rate = $standardRate;
            if ($bookingDate && empty($primaryBooking->lr_no)) {
                $primaryBooking->booking_date = $bookingDate;
            }
            $primaryBooking->save();
            $booking = $primaryBooking;
        } else {
            $booking = TransportBooking::create([
                'sales_invoice_id' => $invoice->id,
                'customer_id' => $customer ? $customer->id : null,
                'box_number' => '1/1',
                'box_index' => 1,
                'total_boxes' => 1,
                'quantity' => $totalQty,
                'preferred_transport' => $preferredTransport,
                'booking_office' => $bookingOffice,
                'booking_rate' => $standardRate,
                'booking_date' => $bookingDate,
                'status' => 'Ready for Delivery',
                'payment_status' => 'Unapproved',
                'created_by' => auth()->id() ?? 1,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }

        if ($customer) {
            $effectiveDate = $invoice->tran_doc_date 
                ? Carbon::parse($invoice->tran_doc_date)->format('Y-m-d') 
                : ($invoice->inv_date ? Carbon::parse($invoice->inv_date)->format('Y-m-d') : Carbon::today()->format('Y-m-d'));

            $this->syncCustomerBoxesForDate($customer->id, $effectiveDate);

            $origTranDate = $invoice->getOriginal('tran_doc_date');
            if ($origTranDate && Carbon::parse($origTranDate)->format('Y-m-d') !== $effectiveDate) {
                $this->syncCustomerBoxesForDate($customer->id, Carbon::parse($origTranDate)->format('Y-m-d'));
            }

            $origInvDate = $invoice->getOriginal('inv_date');
            if ($origInvDate && Carbon::parse($origInvDate)->format('Y-m-d') !== $effectiveDate) {
                $this->syncCustomerBoxesForDate($customer->id, Carbon::parse($origInvDate)->format('Y-m-d'));
            }

            $booking->refresh();
        }

        return collect([$booking]);
    }

    public function syncCustomerBoxesForDate($customerId, $date)
    {
        $customer = \App\Models\Customer::find($customerId);
        if ($customer && (
            stripos($customer->name, 'CASH') !== false ||
            stripos($customer->name, 'ARUMUGAM') !== false ||
            stripos($customer->name, 'SRIVARSAN') !== false
        )) {
            return;
        }

        $dateStr = Carbon::parse($date)->format('Y-m-d');

        $bookings = TransportBooking::where('customer_id', $customerId)
            ->where(function ($q) use ($dateStr) {
                $q->whereDate('booking_date', $dateStr)
                  ->orWhere(function ($sub) use ($dateStr) {
                      $sub->whereNull('booking_date')
                          ->whereHas('salesInvoice', function ($si) use ($dateStr) {
                              $si->whereDate('tran_doc_date', $dateStr)
                                 ->orWhere(function ($s2) use ($dateStr) {
                                     $s2->whereNull('tran_doc_date')
                                        ->whereDate('inv_date', $dateStr);
                                 });
                          });
                  });
            })
            ->orderBy('sales_invoice_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalBoxes = $bookings->count();
        if ($totalBoxes === 0) {
            return;
        }

        foreach ($bookings as $idx => $b) {
            $index = $idx + 1;
            $b->box_index = $index;
            $b->total_boxes = $totalBoxes;
            $b->box_number = "{$index}/{$totalBoxes}";
            $b->save();
        }
    }

    public function syncCustomerBoxesForDispatchDate($customerId, $dispatchDate)
    {
        $customer = \App\Models\Customer::find($customerId);
        if ($customer && (
            stripos($customer->name, 'CASH') !== false ||
            stripos($customer->name, 'ARUMUGAM') !== false ||
            stripos($customer->name, 'SRIVARSAN') !== false
        )) {
            return;
        }

        $dateStr = Carbon::parse($dispatchDate)->format('Y-m-d');

        $bookedBookings = TransportBooking::where('customer_id', $customerId)->where('status', 'Booked')->whereDate('booking_date', $dateStr)->orderBy('sales_invoice_id', 'asc')->orderBy('id', 'asc')->get();

        $totalBoxes = $bookedBookings->count();
        if ($totalBoxes === 0) {
            return;
        }

        foreach ($bookedBookings as $idx => $b) {
            $index = $idx + 1;
            $b->box_index = $index;
            $b->total_boxes = $totalBoxes;
            $b->box_number = "{$index}/{$totalBoxes}";
            $b->saveQuietly();
        }
    }

    public function recordLrBooking($bookingId, array $data)
    {
        $booking = TransportBooking::with(['salesInvoice', 'customer'])->findOrFail($bookingId);

        $oldBookingDate = $booking->booking_date ? Carbon::parse($booking->booking_date)->format('Y-m-d') : null;

        $lrNo = trim($data['lr_no'] ?? '');

        if (empty($lrNo) && ($data['status'] ?? '') === 'Booked') {
            throw ValidationException::withMessages([
                'lr_no' => ['MANDATORY POLICY: NO LR NUMBER = NO PAYMENT CLEARANCE. An LR Number is strictly required to mark this parcel as Booked.']
            ]);
        }

        $booking->lr_no = $lrNo;
        $booking->lr_price = isset($data['lr_price']) && is_numeric($data['lr_price']) ? (float)$data['lr_price'] : ($booking->booking_rate ?: 0);
        $booking->status = $data['status'] ?? 'Booked';
        
        if (!empty($data['preferred_transport'])) {
            $booking->preferred_transport = $data['preferred_transport'];
        }

        if (array_key_exists('delivery_agent_id', $data)) {
            $booking->delivery_agent_id = $data['delivery_agent_id'];
        }
        if (array_key_exists('delivery_agent_name', $data)) {
            $booking->delivery_agent_name = $data['delivery_agent_name'];
        }

        if (!empty($data['booking_date'])) {
            $booking->booking_date = Carbon::parse($data['booking_date']);
        } else {
            $booking->booking_date = Carbon::today();
        }

        if (!empty($data['booking_time'])) {
            $booking->booking_time = $data['booking_time'];
        }

        if (!empty($data['remarks'])) {
            $booking->remarks = $data['remarks'];
        }

        if (!empty($data['box_number'])) {
            $booking->box_number = trim($data['box_number']);
            if (preg_match('/^(\d+)\/(\d+)$/', $booking->box_number, $matches)) {
                $booking->box_index = (int)$matches[1];
                $booking->total_boxes = (int)$matches[2];
            }
        }

        $booking->updated_by = auth()->id() ?? 1;
        $booking->save();

        $newBookingDate = $booking->booking_date ? Carbon::parse($booking->booking_date)->format('Y-m-d') : null;
        if ($booking->customer_id && $newBookingDate) {
            $this->syncCustomerBoxesForDate($booking->customer_id, $newBookingDate);

            if ($oldBookingDate && $oldBookingDate !== $newBookingDate) {
                $this->syncCustomerBoxesForDate($booking->customer_id, $oldBookingDate);
            }
        }
        $booking->refresh();

        $distinctLrs = TransportBooking::where('sales_invoice_id', $booking->sales_invoice_id)->whereNotNull('lr_no')->where('lr_no', '!=', '')->pluck('lr_no')->unique()->implode(', ');

        $allBooked = TransportBooking::where('sales_invoice_id', $booking->sales_invoice_id)->where('status', '!=', 'Booked')->doesntExist();

        $updateData = [];

        if (!empty($distinctLrs)) {
            $updateData['tran_doc_no'] = $distinctLrs;
            $updateData['lr_no'] = $distinctLrs;
            $updateData['vehicle_no'] = $distinctLrs;
        }

        if (!empty($booking->booking_date)) {
            $updateData['tran_doc_date'] = $booking->booking_date;
        }

        if (!empty($booking->preferred_transport) && $booking->preferred_transport !== '-') {
            $updateData['transporter_name'] = $booking->preferred_transport;
        }

        if ($allBooked) {
            $updateData['delivery_status'] = 'Dispatched';
        }

        if (!empty($updateData)) {
            $booking->salesInvoice()->update($updateData);
        }

        return $booking;
    }
}
