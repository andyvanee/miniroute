MiniRoute
========================================

A minimal router/application framework for PHP.

### Install

    composer require andyvanee/miniroute

### Usage

There are a million ways that you could set up your application, but the
recommended structure is something like the following:

- src/MyApp/Controller
    - HomeController.php
- public
    - index.php
    - configuration.php
    - routes.php

#### `src/MyApp/Controller/HomeController.php`

```php
namespace MyApp\Controller;

class HomeController {
    public function __construct($container) {
        // Setup anything that is common to all routes in this controller
    }
    public function index($request, $response) {
        // Handle this route
    }
}
```

#### `public/index.php`

```php
require '../vendor/autoload.php';

$app = new MiniRoute\MiniRoute;
require 'configuration.php';
require 'routes.php';
$app->run();
```

#### `public/configuration.php`
```php
$app->register('helloservice', function() {
    return 'Hello Service';
});
```

#### `public/routes.php`
```php
use MyApp\Controller\HomeController;
$app->route('GET', '/', [HomeController::class, 'index']);
```

If you want to have more control over application-wide behaviour and
functionality, you can either subclass `MiniRoute`, or create a base
class such as `AppController` that all of your controllers will inherit from.

### Run Tests

    cd andyvanee/miniroute && composer run test
