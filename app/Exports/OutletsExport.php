<?php

namespace Modules\Outlet\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Outlet\Models\Outlet;

class OutletsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Outlet::query()->with('typeOutlet');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['type_outlet_id'])) {
            $query->where('type_outlet_id', $this->filters['type_outlet_id']);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'Name',
            'Type',
            'Address',
            'Phone',
            'Email',
            'Status',
            'Schedule Mode',
            'Schedule Status',
            'Created At',
        ];
    }

    public function map($outlet): array
    {
        return [
            $outlet->name,
            $outlet->typeOutlet?->name_type,
            $outlet->address,
            $outlet->phone,
            $outlet->email,
            $outlet->status,
            $outlet->schedule_mode,
            $outlet->schedule_status,
            $outlet->created_at?->format('Y-m-d H:i:s'),
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
