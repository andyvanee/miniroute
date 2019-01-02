<?php

namespace MiniRoute;

class MiniRoute {
    private $callbacks = [
        'GET' => [],
        'POST' => [],
        'HEAD' => []
    ];

    private $classes = [];

    public function __construct() {
        $this->container = new Container;
    }

    public function register($name, $callback) {
        $this->container->register($name, $callback);
    }

    public function route($method, $path, $callback) {
        if (is_array($callback)) {

            [$cls, $fn] = $callback;

            if (!array_key_exists($cls, $this->classes)) {
                $this->classes[$cls] = new $cls($this->container);
            }

            $callback = function($req, $res, $data=[]) use ($cls, $fn) {
                $this->classes[$cls]->$fn($req, $res, $data);
            };
        }

        if (is_array($method)) {
            foreach ($method as $m) {
                $this->route($m, $path, $callback);
            }
        } else {
            $this->callbacks[$method][] = new Route($path, $callback);
        }
    }

    public function run() {
        $this->request = new Request;
        $this->response = new Response;

        $handled = false;

        foreach($this->callbacks[$this->request->method] as $route) {
            if ($match = $route->match($this->request->path)) {
                $handled = true;
                call_user_func_array(
                    [$route, 'call'],
                    [$this->request, $this->response, ['matches' => $match]]
                );
                break;
            }
        }

        if (!$handled) {
            http_response_code(400);
            echo "Page not found";
        }
    }
}
