<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

use RestProxy\RestProxy;
use RestProxy\CurlWrapper;


// Init proxy
$proxy = new RestProxy(Request::createFromGlobals(), new CurlWrapper());
// Setup proxy URL
$proxy->register('/', 'https://news.ycombinator.com');
// Run proxy
$proxy->run();

echo $proxy->getContent();
