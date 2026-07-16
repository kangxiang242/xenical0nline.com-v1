<?php

namespace App\Exports;

use App\Models\Order;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class OrdersExport extends DefaultValueBinder  implements FromArray,WithCustomValueBinder,WithEvents
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

    public function bindValue(Cell $cell, $value)
    {


        if (is_numeric($value) && $cell->getColumn() != 'D') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }


        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function registerEvents() :array
    {
        return [
            AfterSheet::class=>function(AfterSheet $event){
                //设置区域单元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:Z1265')->getAlignment()->setVertical('center');
                //设置区域单元格水平居中
                $event->sheet->getDelegate()->getStyle('A1:Z1265')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle("D2:D1265")->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(30);
            }
        ];
    }
}
