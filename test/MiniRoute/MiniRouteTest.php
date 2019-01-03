<?php

namespace MiniRoute;

use PHPUnit\Framework\TestCase;

class MyController {
    public function __construct($container) {}

    public function index($request, $response) {
        echo 'Hello World';
    }
}

class MiniRouteTest extends TestCase {
    protected function setUp() {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->app = new MiniRoute;
        $this->app->route('GET', '/', [MyController::class, 'index']);
    }

    public function testEchoOutput() {
        $output = "";
        ob_start();
        $this->app->run();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('Hello World', $output);
    }

    /**
     * Route ordering follows Nginx routing rules
     * - Pattern routes take precedence and are matched in the order that they
     *   were declared in configuration.
     * - Prefix routes come next and match the most specific matching route prefix
     */
    public function testRouteOrdering() {
        $this->app->route('GET', '/', [MyController::class, 'debug']);
        $this->app->route('GET', new REPath('|/test/another/(\d+)|'), [MyController::class, 'index']);
        $this->app->route('GET', new REPath('|/test/this/(\d+)|'), [MyController::class, 'index']);
        $this->app->route('GET', '/test/this', [MyController::class, 'index']);
        $this->app->route('GET', '/test', [MyController::class, 'index']);
        $routes = $this->app->getCallbacks()['GET'];

        $this->assertEquals(6, count($routes));

        // Regex routes are prioritized by input order
        $this->assertEquals(new REPath('|/test/another/(\d+)|'), $routes[0]->path);
        $this->assertEquals(new REPath('|/test/this/(\d+)|'), $routes[1]->path);

        // String routes are prioritized by length
        $this->assertEquals('/test/this', $routes[2]->path);
        $this->assertEquals('/test', $routes[3]->path);
        $this->assertEquals('/', $routes[4]->path);
        $this->assertEquals('/', $routes[5]->path);

        // Duplicate routes are appended, and are therefore no-ops
        $this->assertEquals('debug', $routes[5]->fn);
    }
}
