<?php

declare(strict_types=1);

namespace RestProxy;

use Symfony\Component\HttpFoundation\Request;
use Exception;

/**
 * Class RestProxy
 * @package RestProxy
 */
final class RestProxy
{
    private Request $request;
    private CurlWrapper $curl;
    private array $map;
    private string $content;

    public array $headers;

    /**
     * Proxy method | Used only GET
     */
    const OPTIONS = 'OPTIONS';
    const DELETE  = 'DELETE';
    const POST    = 'POST';
    const GET     = 'GET';
    const PUT     = 'PUT';

    /**
     * @var array|string[]
     */
    private array $actions = [
        self::GET     => 'doGet',
        self::POST    => 'doPost',
        self::DELETE  => 'doDelete',
        self::PUT     => 'doPut',
        self::OPTIONS => 'doOptions',
    ];

    /**
     * @param Request $request
     * @param CurlWrapper $curl
     */
    public function __construct(Request $request, CurlWrapper $curl)
    {
        $this->request = $request;
        $this->curl    = $curl;
    }

    /**
     * Setup proxy site URL
     *
     * @param $name
     * @param $url
     */
    public function register($name, $url): void
    {
        $this->map[$name] = $url;
    }

    /**
     * Main proxy func
     *
     * @throws Exception
     */
    public function run(): bool
    {
        $url = $this->request->getPathInfo();

        foreach ($this->map as $name => $mapUrl) {
            if (strpos($url, $name) == 1 || $name == "/") {
                return $this->dispatch($mapUrl . str_replace("/{$name}", '', $url));
            }
        }
        throw new Exception("Not match");
    }

    /**
     * Modify content
     *
     * @return string
     */
    public function getContent(): string
    {
        // Replace assets URL to proxy
        $this->content = str_replace('type="text/css" href="','type="text/css" href="'. 'https://news.ycombinator.com'.'/', $this->content);
        $this->content = str_replace('<img src="','<img src="'. 'https://news.ycombinator.com'.'/', $this->content);
        $this->content = str_replace('type="text/javascript" src="','type="text/javascript" src="'. 'https://news.ycombinator.com'.'/', $this->content);

        // Added  ™ text
        $TMTextPattern = '/ [0-9a-zA-Z]{6,} /';
        $this->content = preg_replace($TMTextPattern, '$0™ ', $this->content);
        return $this->content;
    }

    /**
     * Setup proxy
     *
     * @throws Exception
     */
    private function dispatch($url): bool
    {
        $queryString   = $this->request->getQueryString();
        $action        = $this->getActionName($this->request->getMethod());
        $this->content = $this->curl->$action($url, $queryString);
        $this->headers = $this->curl->getHeaders();
        return true;
    }

    /**
     * Setup request method
     *
     * @throws Exception
     */
    private function getActionName($requestMethod): string
    {
        if (!array_key_exists($requestMethod, $this->actions)) {
            throw new Exception("Method not allowed");
        }
        return $this->actions[$requestMethod];
    }
}
