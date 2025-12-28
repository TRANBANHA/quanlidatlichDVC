<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StaffTemplateExport implements FromArray, WithHeadings, WithStyles, WithCustomCsvSettings
{
    public function array(): array
    {
        // Dữ liệu mẫu
        return [
            [
                'Nguyễn Văn An',
                'nguyenvanan',
                '123456',
                'nguyenvanan@example.com',
                '0901234567'
            ],
            [
                'Trần Thị Bình',
                'tranthibinh',
                '123456',
                'tranthibinh@example.com',
                '0902345678'
            ],
            [
                'Lê Văn Cường',
                'levancuong',
                '123456',
                'levancuong@example.com',
                '0903456789'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Họ tên',
            'Tên đăng nhập',
            'Mật khẩu',
            'Email',
            'Số điện thoại'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style cho header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Cấu hình CSV để hỗ trợ tiếng Việt với UTF-8 BOM
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'line_ending' => PHP_EOL,
            'use_bom' => true, // Bật BOM để Excel hiển thị đúng tiếng Việt
            'include_separator_line' => false,
            'excel_compatibility' => false,
            'output_encoding' => 'UTF-8',
        ];
    }
}

