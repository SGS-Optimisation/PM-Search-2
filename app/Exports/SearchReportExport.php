<?php

namespace App\Exports;

use App\Models\Search;
use App\Operations\PrepareSearchReportOperation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SearchReportExport implements FromArray, WithHeadings, WithMapping, ShouldAutoSize
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
        return $this->report_preparator->headers;
    }

    public function map($data): array
    {
        return [
            $data['tag'],
            $data['account_manager_name'],
            $data['barcode_number'],
            $data['barcode_type'],
            $data['book'],
            $data['booked_date'],
            $data['dieline'],
            $data['brand'],
            $data['color_name'],
            $data['color_type'],
            $data['contact_type'],
            $data['customer_design_ref'],
            $data['customer_name'],
            $data['customer_type'],
            $data['description'],
            $data['file_date'],
            $data['file_location'],
            $data['file_name'],
            $data['font_name'],
            $data['formatted_job_number'],
            $data['hex_colors'],
            $data['job_relationship'],
            $data['job_version_id'],
            $data['language_count'],
            $data['languages'],
            $data['layer_name'],
            $data['number_of_colors'],
            $data['order_type'],
            $data['package_type'],
            $data['packaging_reference'],
            $data['pcm_type_desc'],
            $data['pcm_type_profile_name'],
            $data['plate_thickness'],
            $data['plate_type'],
            $data['portfolio_group_name'],
            $data['print_process'],
            $data['printer_spec_url'],
            $data['project_name'],
            $data['promotion'],
            $data['range_name'],
            $data['score'],
            $data['simplified_group_name'],
            $data['site_name'],
            $data['substrate'],
            $data['tag'],
            $data['url'],
            $data['variety'],
            $data['weight'],
            $data['image_lrg'],
            $data['image_sml'],
            //$data['printer_name'],
            //$data['print_orientation'],
            //$data['screen_resolution'],
        ];

    }


}
