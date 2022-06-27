Simple Rest Proxy

Use example
=========================

```
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

use RestProxy\RestProxy;
use RestProxy\CurlWrapper;

$proxy = new RestProxy(
    Request::createFromGlobals(),
    new CurlWrapper()
    );
$proxy->register('/', 'https://news.ycombinator.com');
$proxy->run();


foreach($proxy->getHeaders() as $header) {
    header($header);
}
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

