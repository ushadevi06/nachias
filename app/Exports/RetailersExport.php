<?php

namespace App\Exports;

use App\Models\Retailer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RetailersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $count = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Retailer::with(['zone', 'state', 'city', 'place'])->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Phone Number',
            'State',
            'City',
            'Place',
            'Zone',
        ];
    }

    public function map($retailer): array
    {
        $this->count++;
        return [
            $this->count,
            $retailer->name . ' (' . ($retailer->code ?? '-') . ')',
            $retailer->email ?? '-',
            $retailer->mobile_number ?? '-',
            $retailer->state->state_name ?? '-',
            $retailer->city->city_name ?? '-',
            $retailer->place->place_name ?? '-',
            $retailer->zone->zone_name ?? '-',
        ];
    }
}
