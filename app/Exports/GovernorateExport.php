<?php

namespace App\Exports;

use App\Models\Governorate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GovernorateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Governorate::with('regions', 'townships', 'villages')->get()->map(function ($item) {
            return [
                'ID' => $item->id,
                'Name' => $item->name,
                'Regions' => $item->regions->pluck('name')->implode(', '),
                'Townships' => $item->townships->pluck('name')->implode(', '),
                'Villages' => $item->villages->pluck('name')->implode(', '),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Regions',
            'Townships',
            'Villages',
        ];
    }
}
