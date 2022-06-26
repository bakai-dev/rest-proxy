<?php

declare(strict_types=1);

namespace RestProxy;

use Exception;

/**
 * Class CurlWrapper
 * @package RestProxy
 */
class CurlWrapper
{
    private array $requestHeaders;
    private array $options = [];
    private int $status;


    private  $responseHeaders = [];
    const HTTP_OK = 200;
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0';


    /**
     * @param array $requestHeaders
     * @param array $options
     */
    function __construct(array $requestHeaders = [], array $options = [])
    {
        if (count($requestHeaders) > 0 && is_array($requestHeaders)) {
            $this->requestHeaders   = $requestHeaders;
            $this->requestHeaders[] = "User-Agent: " . self::USER_AGENT;
        } else {
            $this->requestHeaders = ["User-Agent: " . self::USER_AGENT];
        }
        if (count($options) > 0 && is_array($options)) {
            $this->options = $options;
        }
    }


    /**
     * Setup Get proxy method
     *
     * @param $url
     * @param $queryString
     * @return mixed
     * @throws Exception
     */
    public function doGet($url, $queryString = NULL)
    {
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, is_null($queryString) ? $url : $url . '?' . $queryString);

        return $this->doMethod($s);
    }

    /**
     * Setup Post proxy method
     *
     * @param $url
     * @param $queryString
     * @return mixed
     * @throws Exception
     */
    public function doPost($url, $queryString = NULL)
    {
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_POST, TRUE);
        if (!is_null($queryString)) {
            curl_setopt($s, CURLOPT_POSTFIELDS, parse_str($queryString));
        }

        return $this->doMethod($s);
    }

    /**
     * Setup Put proxy method
     *
     * @param $url
     * @param $queryString
     * @return mixed
     * @throws Exception
     */
    public function doPut($url, $queryString = NULL)
    {
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_CUSTOMREQUEST, 'PUT');
        if (!is_null($queryString)) {
            curl_setopt($s, CURLOPT_POSTFIELDS, parse_str($queryString));
        }

        return $this->doMethod($s);
    }

    /**
     * Setup Delete proxy method
     *
     * @param $url
     * @param $queryString
     * @return mixed
     * @throws Exception
     */
    public function doDelete($url, $queryString = NULL)
    {
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, is_null($queryString) ? $url : $url . '?' . $queryString);
        curl_setopt($s, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!is_null($queryString)) {
            curl_setopt($s, CURLOPT_POSTFIELDS, parse_str($queryString));
        }

        return $this->doMethod($s);
    }

    /**
     * Setup proxy method
     *
     * @param $s
     * @return mixed
     * @throws Exception
     */
    private function doMethod($s)
    {
        curl_setopt($s, CURLOPT_HTTPHEADER, $this->requestHeaders);
        curl_setopt($s, CURLOPT_HEADER, TRUE);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, TRUE);
        foreach ($this->options as $option => $value) {
            curl_setopt($s, $option, $value);
        }
        $out                   = curl_exec($s);
        $this->status          = curl_getinfo($s, CURLINFO_HTTP_CODE);
        $this->responseHeaders = curl_getinfo($s, CURLINFO_HEADER_OUT);
        curl_close($s);

        list($this->responseHeaders, $content) = $this->decodeOut($out);
        if ($this->status != self::HTTP_OK) {
            throw new Exception("http error: {$this->status}", $this->status);
        }

        return $content;
    }

    /**
     * @param $out
     * @return array
     */
    private function decodeOut($out): array
    {
        // It should be a fancy way to do that :(
        $headersFinished = FALSE;
        $headers         = $content = [];
        $data            = explode("\n", $out);
        foreach ($data as $line) {
            if (trim($line) == '') {
                $headersFinished = TRUE;
            } else {
                if ($headersFinished === FALSE && strpos($line, ':') > 0) {
                    $headers[] = $line;
                }

                if ($headersFinished) {
                    $content[] = $line;
                }
            }
        }

        return [$headers, implode("\n", $content)];
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->responseHeaders;
    }
}
