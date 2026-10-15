<?php

namespace App\Services;

use App\Models\SalesInvoice;
use App\Models\TransportBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransportBookingService
{
    /**
     * Generate or sync transport bookings for an invoice when created or marked Ready for Delivery
     */
    public function generateBookingsForInvoice(SalesInvoice $invoice, array $customBoxQuantities = [])
    {
        $numBoxes = max(1, (int)($invoice->no_of_box ?: 1));
        $totalQty = (float)$invoice->items->sum('quantity');
        
        $customer = $invoice->customer;
        $preferredTransport = $invoice->transporter_name ?: ($customer->transport_name ?? 'None');
        $bookingOffice = $customer->booking_office ?? 'None';
        $standardRate = (float)($customer->transport_rate ?? 0);

        // Do not recreate if already confirmed Booked
        $existingBooked = TransportBooking::where('sales_invoice_id', $invoice->id)
            ->whereIn('status', ['Booked'])
            ->exists();

        if ($existingBooked) {
            return TransportBooking::where('sales_invoice_id', $invoice->id)->get();
        }

        TransportBooking::where('sales_invoice_id', $invoice->id)->delete();

        // Calculate quantities per box
        $boxQuantities = [];
        if (!empty($customBoxQuantities) && count($customBoxQuantities) === $numBoxes) {
            $boxQuantities = $customBoxQuantities;
        } else {
            $baseQty = floor($totalQty / $numBoxes);
            $remainder = $totalQty - ($baseQty * $numBoxes);

            for ($i = 1; $i <= $numBoxes; $i++) {
                $qty = $baseQty + ($i === $numBoxes ? $remainder : 0);
                $boxQuantities[$i] = $qty;
            }
        }

        $createdBookings = [];
        DB::transaction(function () use ($invoice, $customer, $numBoxes, $boxQuantities, $preferredTransport, $bookingOffice, $standardRate, &$createdBookings) {
            for ($index = 1; $index <= $numBoxes; $index++) {
                $boxNumber = "{$index}/{$numBoxes}";
                $qty = $boxQuantities[$index] ?? 0;

                $createdBookings[] = TransportBooking::create([
                    'sales_invoice_id' => $invoice->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'box_number' => $boxNumber,
                    'box_index' => $index,
                    'total_boxes' => $numBoxes,
                    'quantity' => $qty,
                    'preferred_transport' => $preferredTransport,
                    'booking_office' => $bookingOffice,
                    'booking_rate' => $standardRate,
                    'status' => 'Ready for Delivery',
                    'payment_status' => 'Pending',
                    'created_by' => auth()->id() ?? 1,
                    'created_at' => $invoice->created_at ?: now(),
                    'updated_at' => $invoice->updated_at ?: now(),
                ]);
            }
        });

        return collect($createdBookings);
    }

    /**
     * Record LR number and confirm booking with strict validation guardrails
     */
    public function recordLrBooking($bookingId, array $data)
    {
        $booking = TransportBooking::with(['salesInvoice', 'customer'])->findOrFail($bookingId);

        $lrNo = trim($data['lr_no'] ?? '');

        // STRICT POLICY: NO LR NUMBER = NO PAYMENT CLEARANCE
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

        // Check if all boxes for this sales invoice are booked -> then update delivery_status to 'Dispatched'
        $allBooked = TransportBooking::where('sales_invoice_id', $booking->sales_invoice_id)
            ->where('status', '!=', 'Booked')
            ->doesntExist();

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
