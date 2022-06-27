Simple Rest Proxy

Use example
=========================

```
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

```

How to install with Docker:
=========================
Install docker containers:
```
docker-compose up --build -d
```

Install composer dependency

```
make init
```

Open a web browser and type: http://localhost:8232/item?id=13713480



How to use local PHP CLI:
=========================
Install composer:
```
composer install
```

Run dummy server (local PHP CLI)

```
php -S localhost:8232 public/index.php 
```

Open a web browser and type: http://localhost:8232/item?id=13713480

