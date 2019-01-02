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
}
