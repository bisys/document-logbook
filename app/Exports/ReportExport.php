<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Document Type',
            'Number',
            'Document Number',
            'User Name',
            'Department',
            'Document Status',
            'Has Revision',
            'Created Date',
            'Hardfile Received Date',
            'Payment Status',
        ];
    }

    public function map($row): array
    {
        static $index = 1;

        return [
            $index++,
            $row['document_type'],
            $row['number'],
            $row['document_number'],
            $row['user_name'],
            $row['department'],
            $row['status'],
            $row['has_revision'],
            $row['created_at'],
            $row['hardfile_received_date'],
            $row['payment_receipt'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
