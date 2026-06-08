<?php

namespace App\Exports;

use App\Models\RawMaterial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RawMaterialsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $count = 0;
    protected $categoryId;

    public function __construct($categoryId = null)
    {
        $this->categoryId = $categoryId;
    }

    public function collection()
    {
        $query = RawMaterial::with(['storeCategory', 'uom', 'createdByUser'])->orderBy('id', 'desc');

        if ($this->categoryId) {
            $query->where('store_category_id', $this->categoryId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Store Category',
            'Code',
            'Name',
            'UOM',
            'Material Type',
            'Specification',
            'Min Stock',
            'Status',
            'Created By',
        ];
    }

    public function map($material): array
    {
        $this->count++;
        return [
            $this->count,
            $material->storeCategory->category_name ?? '-',
            $material->code ?? '-',
            $material->name ?? '-',
            $material->uom->uom_code ?? '-',
            $material->material_type ?? '-',
            $material->specification ?? '-',
            $material->min_stock ?? '0',
            $material->status ?? '-',
            createdByName($material->created_by),
        ];
    }

    public function title(): string
    {
        return 'Raw Materials';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFD9E1F2']],
            ],
        ];
    }
}
