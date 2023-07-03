<?php


namespace App\Services\Photon\Api;


use App\Services\Photon\Api\AuthApi;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BaseApi
{

    public static string $api_name;

    public static function buildBaseUrl($url = null)
    {
        return str_replace(
            ['{api_name}', '{version}'],
            [static::$api_name, nova_get_setting('photon_api_version')],
            $url ?? nova_get_setting('photon_api_base_path')
        );
    }

    public static function buildRequest(): PendingRequest
    {
        AuthApi::appAuth();

        return Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => nova_get_setting('photon_subscription_key'),
            'Ocp-Apim-Trace' => 'true',
            'Cache-Control' => 'no-cache',
        ])->withToken(Cache::get('photon_token'));
    }

    /**
     * @param $api_action
     * @param $query
     * @param array $params
     * @param bool $array_mode
     * @return mixed
     */
    public static function get($api_action, $query, ?array $params = [], $array_mode = false)
    {
        return static::httpVerbWrapper($api_action, $query, $params, $array_mode, 'get');

    }

    /**
     * @param $api_action
     * @param $query
     * @param $params
     * @param bool $array_mode
     * @return mixed
     */
    public static function post($api_action, $query = '', $params = [], $array_mode = false)
    {
        return static::httpVerbWrapper($api_action, $query, $params, $array_mode, 'post');
    }

    protected static function httpVerbWrapper($api_action, $query, $params, $array_mode = false, $method = 'get')
    {
        $url = static::buildBaseUrl() . $api_action . $query;
        $parsed_response = Cache::get($key = $url . print_r($params, true));

        /*
         * If cached response is an error, get rid of it
         */
        if (!$parsed_response) {
            Cache::forget($key);
            $parsed_response = static::call($url, $params, $array_mode, $method);
        }

        return $parsed_response;
    }

    protected static function call($url, $params, $array_mode = false, $method = 'get')
    {
        logger('calling ' . $url . ' with params ' . print_r($params, true));

        return Cache::remember(
            $url . print_r($params, true),
            Carbon::now()->addMinutes(nova_get_setting('photon_api_cache_duration')),
            function () use ($url, $params, $array_mode, $method) {
                $response = static::buildRequest()->$method($url, $params);

                if ($response->status() > 203) {
                    /* no content or error */
                    return false;
                }

                logger('response: ' . $response->body());

                return static::parseResponse($response, $array_mode);
            }
        );
    }

    /**
     * @param Response $response
     * @param $array_mode
     * @return mixed
     */
    public static function parseResponse($response, $array_mode)
    {
        return !is_array($response->body()) ?
            (json_decode($response->successful() ? $response->body() : "", $array_mode)) :
            $response->body();
    }
}
