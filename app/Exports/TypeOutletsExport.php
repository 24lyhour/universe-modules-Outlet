<?php

namespace Modules\Outlet\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Outlet\Models\TypeOutlet;

class TypeOutletsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = TypeOutlet::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $isActive = $this->filters['status'] === 'active';
            $query->where('is_active', $isActive);
        }

        return $query->orderBy('name_type');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Status',
            'Created At',
        ];
    }

    public function map($typeOutlet): array
    {
        return [
            $typeOutlet->id,
            $typeOutlet->name_type,
            $typeOutlet->description,
            $typeOutlet->is_active ? 'Active' : 'Inactive',
            $typeOutlet->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
