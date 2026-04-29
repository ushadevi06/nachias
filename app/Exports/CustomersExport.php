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
            '#',
            'Name',
            'Code',
            'Category',
            'Email',
            'Mobile No',
            'State',
            'City',
            'Place',
            'Zone',
            'Status',
        ];
    }

    public function map($customer): array
    {
        $this->count++;
        return [
            $this->count,
            $customer->name,
            $customer->code,
            $customer->category,
            $customer->email ?? '-',
            $customer->mobile_no ?? '-',
            $customer->state->state_name ?? '-',
            $customer->city->city_name ?? '-',
            $customer->place->place_name ?? '-',
            $customer->zone->zone_name ?? '-',
            $customer->status,
        ];
    }
}
