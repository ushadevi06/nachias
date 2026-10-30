<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class CustomerSampleExport implements WithHeadings, FromArray
{
    public function headings(): array
    {
        return [
            'Category',
            'Code',
            'Name',
            'Tamil Name',
            'Mobile Number',
            'Email',
            'Website URL',
            'State',
            'City',
            'Place',
            'Tamil Place',
            'Address',
            'Tamil Address',
            'Zip Code',
            'Transport Name',
            'Alternate Transport Name',
            'Booking Office',
            'Zone',
            'Contact Person Name',
            'Contact Mobile No',
            'Contact Email',
            'Status',
            'GST No',
            'Payment Terms',
            'Sales Discount',
            'Box Discount Amount(Per Pcs)'
        ];
    }

    public function array(): array
    {
        return [];
    }
}

