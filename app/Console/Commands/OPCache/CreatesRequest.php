<?php

namespace App\Console\Commands\OPCache;

use Illuminate\Support\Facades\Crypt as Crypt;
use Illuminate\Support\Facades\Http;

trait CreatesRequest
{
    /**
     * @param string $url
     * @param array  $parameters
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function sendRequest($url, $parameters = [])
    {
        return Http::withHeaders(config('opcache.headers'))
            ->withOptions(['verify' => config('opcache.verify')])
            ->get(rtrim(config('opcache.url'), '/') . '/' . trim(config('opcache.prefix'), '/') . '/' . ltrim($url, '/'),
                array_merge(['key' => Crypt::encrypt('opcache')], $parameters)
            );
    }
}
