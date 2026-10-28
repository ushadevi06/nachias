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

        if ($primaryBooking) {
            $primaryBooking->customer_id = $customer ? $customer->id : null;
            $primaryBooking->quantity = $totalQty;
            $primaryBooking->preferred_transport = $preferredTransport;
            $primaryBooking->booking_office = $bookingOffice;
            $primaryBooking->booking_rate = $standardRate;
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
                'status' => 'Ready for Delivery',
                'payment_status' => 'Unapproved',
                'created_by' => auth()->id() ?? 1,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }

        if ($customer) {
            $this->syncCustomerBoxesForDate($customer->id, $booking->created_at);
            $booking->refresh();
        }

        return collect([$booking]);
    }

    public function syncCustomerBoxesForDate($customerId, $date)
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');

        $bookings = TransportBooking::where('customer_id', $customerId)->whereDate('created_at', $dateStr)->orderBy('sales_invoice_id', 'asc')->orderBy('id', 'asc')->get();

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

    public function recordLrBooking($bookingId, array $data)
    {
        $booking = TransportBooking::with(['salesInvoice', 'customer'])->findOrFail($bookingId);

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

        if (!empty($data['delivery_agent_id'])) {
            $booking->delivery_agent_id = $data['delivery_agent_id'];
        }
        if (!empty($data['delivery_agent_name'])) {
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

        $booking->updated_by = auth()->id() ?? 1;
        $booking->save();

        $allBooked = TransportBooking::where('sales_invoice_id', $booking->sales_invoice_id)->where('status', '!=', 'Booked')->doesntExist();

        if ($allBooked) {
            $booking->salesInvoice()->update([
                'delivery_status' => 'Dispatched',
                'transporter_name' => $booking->preferred_transport ?: $booking->salesInvoice->transporter_name,
                'vehicle_no' => $booking->lr_no,
            ]);
        }

        return $booking;
    }
}
