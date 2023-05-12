<?php

return [
    'image_search_url' => env('PM_SEARCH_IMAGE_ENDPOINT'),
    'text_search_url' => env('PM_SEARCH_TEXT_ENDPOINT'),
    'autocomplete_suggester_url' => env('AUTOCOMPLETE_SUGGESTER'),


    'image_search_techs' => [
        'barcodesearch' => ['title' => 'Barcode', 'value' => false, 'position' => 1],
        'visualsearch' => ['title' => 'Visual', 'value' => true, 'position' => 2],
        //'docsearch' => ['title' => 'Document', 'value' => false, 'position' => 3],
        //'entitysearch' => ['title' => 'Entity', 'value' => false, 'position' => 4],
    ],

    'fields-v1' => [
        'tag' => ['search' => true, 'display' => false, 'type' => 'list'],
        'Brand' => ['search' => true, 'display' => true, 'type' => 'list'],
        'JobId' => ['search' => false, 'display' => true, 'type' => 'list'],
        'score' => ['search' => false, 'display' => true, 'type' => 'list'],
        'SiteId' => ['search' => true, 'display' => true, 'type' => 'list'],
        'Variety' => ['search' => true, 'display' => true, 'type' => 'list'],
        'dieline' => ['search' => false, 'display' => true, 'type' => 'list'],
        'ocrtext' => ['search' => false, 'display' => false, 'type' => 'list'],
        'SiteName' => ['search' => false, 'display' => true, 'type' => 'list'],
        'IsGPNSite' => ['search' => false, 'display' => true, 'type' => 'list'],
        'JobStatus' => ['search' => true, 'display' => true, 'type' => 'list'],
        'Promotion' => ['search' => true, 'display' => true, 'type' => 'list'],
        'Substrate' => ['search' => true, 'display' => true, 'type' => 'list'],
        'file_name' => ['search' => false, 'display' => false, 'type' => 'list'],
        'PackTypeId' => ['search' => true, 'display' => true, 'type' => 'list'],
        'BarcodeType' => ['search' => true, 'display' => true, 'type' => 'list'],
        'ContactType' => ['search' => true, 'display' => true, 'type' => 'list'],
        'OrderTypeId' => ['search' => true, 'display' => true, 'type' => 'list'],
        'ProcessType' => ['search' => true, 'display' => true, 'type' => 'list'],
        'CustomerName' => ['search' => true, 'display' => true, 'type' => 'list'],
        'CustomerType' => ['search' => true, 'display' => true, 'type' => 'list'],
        'JobVersionId' => ['search' => false, 'display' => false, 'type' => 'list'],
        'LegalAddress' => ['search' => false, 'display' => true, 'type' => 'list'],
        'BarcodeNumber' => ['search' => false, 'display' => true, 'type' => 'list'],
        'BookedDateTime' => ['search' => true, 'display' => true, 'type' => 'date'],
        'NumberOfColors' => ['search' => false, 'display' => true, 'type' => 'list'],
        'AccountManagerId' => ['search' => false, 'display' => false, 'type' => 'list'],
        'CompanyLegalName' => ['search' => true, 'display' => true, 'type' => 'list'],
        'PortfolioGroupId' => ['search' => false, 'display' => false, 'type' => 'list'],
        'JobRelationshipId' => ['search' => true, 'display' => true, 'type' => 'list'],
        'NumberOfNewColors' => ['search' => false, 'display' => true, 'type' => 'list'],
        'SimplifiedGroupId' => ['search' => false, 'display' => false, 'type' => 'list'],
        'AccountManagerType' => ['search' => true, 'display' => true, 'type' => 'list'],
        'FormattedJobNumber' => ['search' => false, 'display' => true, 'type' => 'list'],
        'PortfolioGroupName' => ['search' => true, 'display' => true, 'type' => 'list'],
        'JobAccountManagerId' => ['search' => false, 'display' => false, 'type' => 'list'],
        'SimplifiedGroupName' => ['search' => true, 'display' => true, 'type' => 'list'],
    ],


    'fields' => [
        //Job Details

        // ROW 1
        'formatted_job_number' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 1, 'section' => 'Job Details'],
        'simplified_group_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 2, 'section' => 'Job Details'],

        // this fields contains a list of actual customers (end users) as well as printers
        'customer_name' => ['search' => true, 'display' => false, 'type' => 'list', 'response_type' => 'list'],
        // this generated field contains only custmers/end users
        'customer_name_only' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 3, 'title'=> 'Customer Name', 'section' => 'Job Details'],

        'brand' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 4, 'section' => 'Job Details'],
        'variety' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 5, 'section' => 'Job Details'],
        'weight' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 6, 'section' => 'Job Details'],

        // ROW, 'section' => 'Job Details' 2
        'booked_date' => ['search' => true, 'display' => true, 'type' => 'date', 'response_type' => 'date', 'position'=> 10, 'section' => 'Job Details'],
        'customer_design_ref' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 11, 'title' => 'Design End User Reference', 'section' => 'Job Details'],
        'order_type' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 12, 'section' => 'Job Details'],
        'package_type' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 13, 'section' => 'Job Details'],
        'account_manager_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 14, 'title' => 'Primary Project Manager', 'section' => 'Job Details'],
        'site_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 15, 'title' => 'Primary SGS Location', 'section' => 'Job Details'],

        // ROW, 'section' => 'Job Details' 3
        'job_relationship' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 20, 'section' => 'Job Details'],
        'languages' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 21, 'section' => 'Job Details'],
        'language_count' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 22, 'section' => 'Job Details'],
        'project_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 23, 'section' => 'Job Details'],
        'range_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 24, 'section' => 'Job Details'],
        'promotion' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 25, 'section' => 'Job Details'],

        // ROW, 'section' => 'Job Details' 4
        'barcode_number' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 31, 'section' => 'Job Details'],
        'barcode_type' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 32, 'section' => 'Job Details'],
        'file_location' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 33, 'section' => 'Job Details'],


        // Printer Specifications

        // ROW 1

        // printer_name is a generated row
        'printer_name' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 1, 'section' => 'Printer Specifications'],
        'print_process' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 2, 'section' => 'Printer Specifications'],
        'packaging_reference' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 3, 'section' => 'Printer Specifications'],
        'printer_spec_url' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 4, 'title' => 'Printer Spec ID', 'section' => 'Printer Specifications'],
        'dieline' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 5, 'section' => 'Printer Specifications'],
        // TODO: this is not present in the API
        'print_orientation' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 6, 'section' => 'Printer Specifications'],

        // ROW 2
        'plate_type' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 10, 'section' => 'Printer Specifications'],
        'plate_thickness' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 11, 'section' => 'Printer Specifications'],
        'substrate' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 12, 'section' => 'Printer Specifications'],

        // Colour Details
        'pcm_type_desc' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 1, 'title' => 'Proof Type', 'section' => 'Colour Details'],
        'pcm_type_profile_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 2, 'title' => 'E-Profile', 'section' => 'Colour Details'],
        'number_of_colors' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'int', 'position'=> 3, 'title' => 'Number Of Colours', 'section' => 'Colour Details'],



        // BRIDGE FIELDS
        'color_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'book' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'color_type' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'hex_colors' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'font_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'layer_name' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],

        // HIDDEN ROWS
        'contact_type' => ['search' => true, 'display' => false, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'customer_type' => ['search' => true, 'display' => false, 'type' => 'list', 'response_type' => 'list', 'position'=> 100],
        'portfolio_group_name' => ['search' => true, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        'job_version_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],

        'description' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        'url' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],


        'file_name' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],

        'score' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'float', 'position'=> 100],
        'tag' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],



        // AVAILABLE IN API BUT NOT USED
        //'job_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'job_status' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'job_relationship_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'site_id' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'portfolio_group_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'simplified_group_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'project_id' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'job_account_manager_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'account_manager_id' => ['search' => false, 'display' => false, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
        //'account_manager_type' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],

        //'number_of_new_colors' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'int', 'position'=> 100],
        //'file_date' => ['search' => false, 'display' => true, 'type' => 'list', 'response_type' => 'date', 'position'=> 100],
        //'ocrtext' => ['search' => true, 'display' => true, 'type' => 'list', 'response_type' => 'str', 'position'=> 100],
    ],

    'bridge_view_fields' => [
        'font_name', 'layer_name',
        //'description',
    ],

    'grouped_fields' => [
        [
            'name' => 'File Attributes',
            'fields' => ['font_name', 'layer_name',]
        ]
    ],

    'tables' => [
        [
            'name' => 'Color Table',
            'section' => 'Colour Details',
            'fields' => [
                'color_name', 'book', 'color_type', 'hex_colors'
            ]
        ]
    ],

    'advanced_search' => [

        'brand' => ['key' => 'brand', 'type' => 'autocomplete'],
        'simplified_group_name' => ['key' => 'simplified', 'type' => 'autocomplete'],

        'variety' => ['key' => 'variety', 'type' => 'autocomplete'],
        'printer_name' => ['key' => 'printer', 'type' => 'autocomplete'],

        'weight' => ['key' => 'weight', 'type' => 'text'],
        'print_process' => ['key' => 'process', 'type' => 'text'],

        'customer_design_ref' => ['key' => 'ref', 'type' => 'text', 'title' => 'Design / End User Reference'],
        'pcm_type_profile_name' => ['key' => 'profile', 'type' => 'text', 'title' => 'E-Number/Profile'],

        'package_type' => ['key' => 'pack', 'type' => 'text'],
        'booked_date' => ['key' => 'date', 'type' => 'date'],


    ],

    'color_name_mappings' => [
        'pms1000c' => 'PANTONE Solid Coated',
        'pms1000u' => 'PANTONE Solid Uncoated',
        'pms1000m' => 'PANTONE Colors Matte',
        'pegc' => 'PANTONE+ Extended Gamut Coated',
        'ppasu' => 'PANTONE Pastels & Neons Uncoated',
        'ppasc' => 'PANTONE Pastels & Neons Coated',
        'pmetc' => 'PANTONE Premium Metallics Coated',
        'goeu' => 'PANTONE GoeGuide Uncoated',
        'goec' => 'PANTONE GoeGuide Coated',
    ]
];
