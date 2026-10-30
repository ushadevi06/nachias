<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $count = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::with(['zone', 'state', 'city', 'place'])->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Tamil Name',
            'Category',
            'Mobile Number',
            'Email',
            'Transport Name',
            'Alternate Transport Name',
            'Booking Office',
            'State',
            'City',
            'Place',
            'Tamil Place',
            'Zone',
            'Address',
            'Tamil Address',
            'Zip Code',
            'GST No',
            'PAN No',
            'Status',
        ];
    }

    public function map($customer): array
    {
        $this->count++;

        return [
            $customer->code ?? '',
            $customer->name ?? '',
            $customer->tamil_name ?? '',
            $customer->category ?? '',
            $customer->mobile_no ?? '',
            $customer->email ?? '',
            $customer->transport_name ?? '',
            $customer->alt_transport_name ?? '',
            $customer->booking_office ?? '',
            $customer->state->state_name ?? '',
            $customer->city->city_name ?? '',
            $customer->place->place_name ?? '',
            $customer->tamil_place ?? '',
            $customer->zone->zone_name ?? '',
            $customer->address_line_1 ?? '',
            $customer->tamil_address ?? '',
            $customer->zip_code ?? '',
            $customer->gst_no ?? '',
            $customer->pan_no ?? '',
            $customer->status ?? 'Active',
        ];
    }
}
