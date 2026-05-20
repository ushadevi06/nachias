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
            'ID (system generated)',
            'Retailer Name*',
            'Owner Name',
            'Retailer Reference ID',
            'CC (country code)',
            'Phone Number',
            'CC (country code)',
            'Other Phone Number',
            'Email',
            'PAN',
            'GSTIN',
            'Address Line 1',
            'Address Line 2',
            'City*',
            'State*',
            'Country*',
            'Zip code',
            'Transporter name',
            'Transporter GSTIN',
            'Transporter Address Line 1',
            'Transporter Address Line 2',
            'Transporter City',
            'Transporter State',
            'Transporter Country',
            'Transporter Zip',
            'Sales Target (In Pcs)',
        ];
    }

    public function map($customer): array
    {
        $this->count++;

        return [
            $this->count,
            $customer->name ?? '-',
            $customer->name ?? '-',
            $customer->code ?? '-',
            '91',
            $customer->mobile_no ?? '-',
            '',
            '',
            $customer->email ?? '-',
            $customer->pan_no ?? '-',
            $customer->gst_no ?? '-',
            $customer->address_line_1 ?? '-',
            $customer->address_line_2 ?? '-',
            $customer->city->city_name ?? '-',
            $customer->state->state_name ?? '-',
            'India',
            $customer->zip_code ?? '-',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '0',
        ];
    }
}
