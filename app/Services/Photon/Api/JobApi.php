<?php

namespace App\Services\Photon\Api;

class JobApi extends BaseApi
{
    public static string $api_name = '';

    public static function jobColours($formattedJobNumber, $params = [], $array_mode = false)
    {
        logger('job colour');
        return static::get(
            'getjobcolour/',
            $formattedJobNumber,
            array_merge(['jobNumber' => $formattedJobNumber], $params),
            $array_mode
        );
    }
}
