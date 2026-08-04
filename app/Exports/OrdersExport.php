<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class OrdersExport extends DefaultValueBinder implements FromArray, WithCustomValueBinder, WithEvents, WithHeadings
{
    protected $order;

    public function __construct(array $order)
    {
        $this->order = $order;
    }

    public function array(): array
    {
        return $this->order;
    }

    public function headings(): array
    {
        return [
            '訂單號', '內單號', '商品', '總價', '名字', '電話', '郵箱', '地址', '收貨方式', '配送時間', '備注', '訂單狀態'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && $cell->getColumn() != 'D') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // First row (headings) bold
                $sheet->getStyle('A1:L1')->getFont()->setBold(true);

                // Vertical and horizontal center for all data
                $sheet->getStyle('A1:L1265')->getAlignment()->setVertical('center');
                $sheet->getStyle('A1:L1265')->getAlignment()->setHorizontal('center');

                // Wrap text for product column (C) and address column (H)
                $sheet->getStyle('C2:C1265')->getAlignment()->setWrapText(true);
                $sheet->getStyle('H2:H1265')->getAlignment()->setWrapText(true);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(60);
                $sheet->getColumnDimension('I')->setWidth(30);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(30);
                $sheet->getColumnDimension('L')->setWidth(15);
            },
        ];
    }
}