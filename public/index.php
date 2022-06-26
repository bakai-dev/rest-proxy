<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

use RestProxy\RestProxy;
use RestProxy\CurlWrapper;

/**
 * Set
 */
$proxy = new RestProxy(Request::createFromGlobals(), new CurlWrapper());

$proxy->register('/', 'https://news.ycombinator.com');

$proxy->run();

echo $proxy->getContent();
