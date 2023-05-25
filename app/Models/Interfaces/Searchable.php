<?php

namespace App\Models\Interfaces;

interface Searchable
{
    const SEARCH_MODE_TEXT = 'text';
    const SEARCH_MODE_IMAGE = 'image';

    const SEARCH_SOURCE_FILENAME = 'source.jpg';
    const SEARCH_THUMB_FILENAME = 'thumb.jpg';

    const FLAG_PROCESSED = 'processed';
    const FLAG_HAS_SOURCE_IMAGE = 'source_as_image';
    const FLAG_ERROR = 'error';


}
