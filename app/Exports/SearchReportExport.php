<?php

namespace App\Exports;

use App\Models\Search;
use App\Operations\PrepareSearchReportOperation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use function Psy\sh;

class SearchReportExport implements FromArray, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{

    public PrepareSearchReportOperation $report_preparator;

    /**
     * SearchReportExport constructor.
     */
    public function __construct(public Search $search)
    {
        $this->report_preparator = (new PrepareSearchReportOperation($this->search))->handle();
    }


    public function array(): array
    {
        return $this->report_preparator->report_output;
    }

    public function headings(): array
    {
        return array_map(
            function($field) {
                return config('pm-search.fields.' .$field . '.title') ?? Str::of($field)->snake()->replace('_', ' ')->title()->value;
            },
            static::get_fields()
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $styleArray = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF00']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin'
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ];
                $sheet->getStyle('A1:AX1')->applyFromArray($styleArray);
            },
        ];
    }

    public static function get_fields(): array
    {
        return [
            'formatted_job_number', 'simplified_group_name', 'customer_name_only', 'brand', 'variety',
            'weight', 'booked_date', 'customer_design_ref', 'order_type', 'package_type', 'account_manager_name',
            'site_name', 'job_relationship', 'languages', 'language_count', 'project_name', 'range_name', 'promotion',
            'barcode_number', 'barcode_type', 'file_location', 'printer_name', 'print_process', 'packaging_reference',
            'printer_spec_url', 'dieline', 'print_orientation', 'plate_type', 'plate_thickness', 'screen_resolution',
            'substrate', 'pcm_type_desc', 'pcm_type_profile_name', 'number_of_colors', 'color_name', 'book', 'color_type',
            'hex_colors', 'font_name', 'layer_name', 'contact_type', 'customer_type', 'portfolio_group_name',
            'job_version_id', 'description', 'url', 'file_name', 'score', 'tag',
        ];
    }

    public function map($data): array
    {
        $fields = [];

        foreach (static::get_fields() as $field) {
            $fields[$field] = $data[$field] ?? null;
        }

        return $fields;
    }


}
