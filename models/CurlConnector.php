<?php

namespace app\models;

/**
 * Class CurlConnector
 * @package app\models
 */
class CurlConnector
{
    public $uri = '';
    public $request_header = array();
    public $request_body = '';
    public $cook_string = '';
    public $content_length = 0;

    /**
     * @param string $uri
     * @param array $request_headers
     * @param string $request_body
     */
    private function setVars($uri, $request_headers, $request_body)
    {
        $this->uri = $uri;
        $this->request_header = $request_headers;
        $this->content_length = self::getContentLength($request_body);
        array_push($this->request_header, 'content-length: '.$this->content_length);
        $this->request_body = $request_body;
    }

    /**
     * @param string $body_request
     * @return int
     */
    private static function getContentLength($body_request)
    {
        return strlen($body_request);
    }

    /**
     * @param object $curl
     */
    private static function setBasicCurlOptions($curl)
    {
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
    }

    /**
     * @param string $result
     * @return string
     */
    private function getCookie($result)
    {
        $cookie_string = '';
        $response_header = explode("\n",$result);

        foreach ($response_header as $part) {
            $parts = explode(":",$part,2);

            if ($parts[0] == 'set-cookie' || $parts[0] == 'Set-Cookie') {
                $parts = explode(";", $parts[1], 2);
                $cookie_string .= $parts[0].';';
            }
        }

        return $cookie_string;
    }

    /**
     * @param string $uri
     * @param array $request_headers
     * @param string $request_body
     * @throws \Exception
     */
    public function logIn($uri, $request_headers, $request_body)
    {
        $curl = curl_init();
        $this->setVars($uri, $request_headers, $request_body);

        self::setBasicCurlOptions($curl);

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->request_header);
        curl_setopt($curl, CURLOPT_URL, $this->uri);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->request_body);

        $result = curl_exec($curl);

        $response_cookie = $this->getCookie($result);
        $this->cook_string = $response_cookie;

        curl_setopt($curl, CURLOPT_HEADER, false);
        $result = curl_exec($curl);

        if ($result === '{"error":{"username":["User not found"]}}') {
            throw new \Exception('User not found');
        }

        curl_close($curl);
    }

    /**
     * @param string $uri
     * @param array $request_headers
     * @param string $request_body
     * @return bool|string
     */
    public function getData($uri, $request_headers, $request_body)
    {
        $curl = curl_init();
        $this->setVars($uri, $request_headers, $request_body);

        self::setBasicCurlOptions($curl);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIE, $this->cook_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->request_header);
        curl_setopt($curl, CURLOPT_URL, $this->uri);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->request_body);

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
