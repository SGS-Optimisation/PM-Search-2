<?php

namespace App\Nova\Actions;

use App\Models\Interfaces\Searchable;
use App\Models\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\ActionRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class ExportUserSearches extends DownloadExcel
    implements WithMapping, WithHeadings
{
    use InteractsWithQueue, Queueable;


    public function headings(): array
    {
        return [
            'User',
            'Mode',
            'Search',

            'Phrase',
            'OCR',
            'Type',

            'Datetime',
            'Link',
        ];
    }

    /**
     * @param  Search  $search
     *
     * @return array
     */
    public function map($search): array
    {
        return [
            $search->user->name,
            $search->search_mode,

            match ($search->search_mode) {
                Searchable::SEARCH_MODE_TEXT => implode(' '.$search->search_data->operator.' ',
                    $search->search_data->search_string),
                Searchable::SEARCH_MODE_IMAGE => $search->working_data['original_filename'] ?? 'Error',
            },

            //phase
            match ($search->search_mode) {
                Searchable::SEARCH_MODE_TEXT => $search->searchDataIsPhrase ?: 'FALSE',
                Searchable::SEARCH_MODE_IMAGE => 'NA'
            },
            //OCR
            match ($search->search_mode) {
                Searchable::SEARCH_MODE_TEXT => $search->searchDataIsOcr ?: 'FALSE',
                Searchable::SEARCH_MODE_IMAGE => 'NA'
            },
            //type
            match ($search->search_mode) {
                Searchable::SEARCH_MODE_TEXT => $search->searchDataType,
                Searchable::SEARCH_MODE_IMAGE => implode(',', array_keys($search->working_data['search_techs']?? []))
            },

            $search->created_at->format('Y-m-d H:i'),

            route('search.show', $search->id),
        ];
    }

}
