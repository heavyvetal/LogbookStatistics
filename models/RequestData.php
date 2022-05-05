<?php

namespace app\models;

class RequestData
{
    public function getTableHeaders()
    {
        return array(
            'accept: application/json, text/plain, */*',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'content-type: application/json;charset=UTF-8',
            //'origin: https://logbook.itstep.org',
            //'referer: https://logbook.itstep.org/',
            //'sec-fetch-mode: cors',
            //'sec-fetch-site: same-origin',
            'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36',
        );
    }

    public function getLoginHeaders()
    {
        return array(
            'accept: application/json, text/plain, */*',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'content-type: application/json;charset=UTF-8',
            //'cookie: ',
            //'origin: https://logbook.itstep.org',
            //'referer: https://logbook.itstep.org/login/index',
            //'sec-fetch-mode: cors',
            //'sec-fetch-site: same-origin',
            'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36',
            'x-requested-with: XMLHttpRequest'
        );
    }

    public function getGroupsPost()
    {
        // Default valid data
        return '{"id_tgroups":"0000","id_spec":"000","limit":0,"offset":0}';
    }
}






