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
            $data['Brand'],
            $data['JobId'],
            $data['score'],
            $data['SiteId'],
            $data['Variety'],
            $data['dieline'],
            $data['ocrtext'] ?? '',
            $data['SiteName'],
            $data['IsGPNSite'],
            $data['JobStatus'],
            $data['Promotion'],
            $data['Substrate'],
            $data['file_name'],
            $data['PackTypeId'],
            $data['BarcodeType'],
            $data['ContactType'],
            $data['OrderTypeId'],
            $data['ProcessType'],
            $data['CustomerName'],
            $data['CustomerType'],
            $data['JobVersionId'],
            $data['LegalAddress'],
            $data['BarcodeNumber'],
            $data['BookedDateTime'],
            $data['NumberOfColors'],
            $data['AccountManagerId'],
            $data['CompanyLegalName'],
            $data['PortfolioGroupId'],
            $data['JobRelationshipId'],
            $data['NumberOfNewColors'],
            $data['SimplifiedGroupId'],
            $data['AccountManagerType'],
            $data['FormattedJobNumber'],
            $data['PortfolioGroupName'],
            $data['JobAccountManagerId'],
            $data['SimplifiedGroupName'],
        ];
    }


}
