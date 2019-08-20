<?php

namespace App\Services\MttApiService;

use Illuminate\Support\Str;
use Ixudra\Curl\CurlService;

abstract class Service {
    final protected function callApi (string $endpoint, string $method = 'get', array $data = null) : ?object {
        $curl = (new CurlService())
            ->to('http://interview.mtt.digital:8081/' . $endpoint)
            ->withHeaders(['auth: B96C622D10588956900BA8D4604A9811'])
            ->asJson();

        if (!empty($data)) {
            $curl->withData($data);
        }

        switch (Str::lower($method)) {
            case 'post':
                return $curl->post();
                break;

            case 'delete':
                return $curl->delete();
                break;

            case 'get':
            default:
                return $curl->get();
        }
    }
}