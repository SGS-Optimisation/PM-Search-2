<?php


namespace App\Services\Photon\Api;


use App\Services\Photon\Api\BaseApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AuthApi extends BaseApi
{

    public static string $api_name = 'authapi';

    public static function appAuth()
    {
        if (
            (!Cache::has('photon_token') && !Cache::has('photon_token_expiry'))
            || Carbon::createFromTimeString(Cache::get('photon_token_expiry'))->lessThan(Carbon::today())
        ) {

            $url = static::buildBaseUrl(nova_get_setting('photon_api_auth_path')).'ApplicationAuthorisation/authenticate';

            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => nova_get_setting('photon_subscription_key'),
                'Ocp-Apim-Trace' => 'true',
                'Cache-Control' => 'no-cache',
            ])->post($url, [
                "appId" => nova_get_setting('photon_api_app_id'),
                "apiKey" => nova_get_setting('photon_api_app_key'),
            ]);

            $tokens = static::parseResponse($response, true);

            Cache::put('photon_token_expiry', $tokens['validTo']);
            Cache::put('photon_token', $tokens['token']);
        }
    }

}
