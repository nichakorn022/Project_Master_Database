<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ShapesTemplateExport implements FromArray, WithHeadings, WithColumnFormatting
{
    public function array(): array
    {
        return [
            // ['a1b2c34',
            // 'ไทย',
            // 'English',
            // 'Bone',
            // 'Active',
            // 'Z19',
            // 'IKEA',
            // 'Cup & Saucer',
            // 'glaze',
            // 'Bob',
            // 'Alice',
            // '99',
            // '150',
            // '23.98',
            // '11.1',
            // '34',
            // '44',
            // '1.2',
            // 'TRUE',
            // '2024-01-01']
        ];
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Description Thai',
            'Description Eng',
            'Type',
            'Status',
            'Collection Code',
            'Customer',
            'Item Group',
            'Process',
            'Designer',
            'Requestor',
            'Volume',
            'Weight',
            'Long Diameter',
            'Short Diameter',
            'Height Long',
            'Height Short',
            'Body',
            'Mold',
            'Approval Date',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
        ];
    }
}
