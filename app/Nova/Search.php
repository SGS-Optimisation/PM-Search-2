<?php

namespace App\Nova;

use App\Exports\SearchReportExport;
use App\Nova\Actions\ExportUserSearches;
use App\Nova\Filters\DateRangeFilter;
use App\Nova\Metrics\SearchByWeekTrend;
use dddeeemmmooonnn\NovaMulticolumnFilter\NovaMulticolumnFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Sparkline;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;


class Search extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\Search::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'search_mode', 'search_data', 'report', 'working_data', 'user_id'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Searches');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Search');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('Id'), 'id')
                ->rules('required')
                ->sortable()
            ,
            BelongsTo::make('Search', 'parent')
                ->searchable()
                ->hideFromIndex()
                ->sortable()
            ,
            Text::make(__('Search Mode'), 'search_mode')
                ->rules('required')
                ->sortable()
            ,
            BelongsTo::make('User')
                ->searchable()
                ->sortable()
            ,
            Stack::make('Details', [
                Text::make('Title'),
                Text::make('SearchDataType'),
            ])->onlyOnIndex(),
            /*Code::make(__('Search Data'), 'search_data')
                ->sortable()
                ->json()
            ,*/
            KeyValue::make('Search Data Kv')->rules('json'),
            Text::make(__('Source Filepath'), 'source_filepath')
                ->sortable()
                ->hideFromIndex()
            ,
            Text::make(__('Image Path'), 'image_path')
                ->sortable()
                ->hideFromIndex()
            ,
            Code::make(__('Working Data'), 'working_data')
                ->sortable()
                ->json()
            ,
            Code::make(__('Report'), 'report')
                ->sortable()
                ->json()
            ,
            DateTime::make(__('Created At'), 'created_at')
                ->sortable()
            ,

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function filters(Request $request)
    {
        return [
            //(new DateRangeFilter),
            /*new NovaMulticolumnFilter([
                'search_mode' => [
                    'type' => 'select',
                    'label' => 'Search mode',
                    'options' => [
                        \App\Models\Search::SEARCH_MODE_TEXT => \App\Models\Search::SEARCH_MODE_TEXT ,
                        \App\Models\Search::SEARCH_MODE_IMAGE => \App\Models\Search::SEARCH_MODE_IMAGE ,
                    ],
                    'defaultValue' => '1',
                ],
                'search_data' => '',
                'working_data' => '',
                'report' => '',
            ],
                $manual_update = false, // Apply filter with the button
                $default_column_type = 'text', // Default input type
                $name = 'Filter Content' // Filter name
            ),
            */
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function actions(Request $request)
    {
        return [
            new ExportUserSearches(),
        ];
    }
}
