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

    public function getCallbacks() {
        $this->sortRoutes();
        return $this->callbacks;
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
            $this->callbacks[$method][] = new Route($path, $callback, $cls, $fn);
        }
    }

    private function sortRoutes() {
        foreach ($this->callbacks as $method => $callbacks) {
            $this->stable_uasort($callbacks, function($a, $b) {
                if (is_string($a->path) && is_object($b->path)) {
                    return 1;
                }

                if (is_object($a->path) && is_string($b->path)) {
                    return -1;
                }

                if (is_object($a->path) && is_object($b->path)) {
                    return 0;
                }

                if (is_string($a->path) && is_string($b->path)) {
                    return $this->compareStrings($a->path, $b->path);
                }
            });
            $this->callbacks[$method] = array_slice($callbacks, 0);
        }
    }

    private function compareStrings($a, $b) {
        $a = strlen($a);
        $b = strlen($b);
        return ($a == $b) ? 0 : ($a < $b) ? 1 : -1;
    }

    public function run() {
        $this->request = new Request;
        $this->response = new Response;

        $handled = false;

        $this->sortRoutes();

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

    /**
     * uasort which retains order when items compare as equal
     */
    private function stable_uasort(&$array, $cmp_function) {
        if(count($array) < 2) {
            return;
        }
        $halfway = count($array) / 2;
        $array1 = array_slice($array, 0, $halfway, TRUE);
        $array2 = array_slice($array, $halfway, NULL, TRUE);

        $this->stable_uasort($array1, $cmp_function);
        $this->stable_uasort($array2, $cmp_function);
        if(call_user_func($cmp_function, end($array1), reset($array2)) < 1) {
            $array = $array1 + $array2;
            return;
        }
        $array = array();
        reset($array1);
        reset($array2);
        while(current($array1) && current($array2)) {
            if(call_user_func($cmp_function, current($array1), current($array2)) < 1) {
                $array[key($array1)] = current($array1);
                next($array1);
            } else {
                $array[key($array2)] = current($array2);
                next($array2);
            }
        }
        while(current($array1)) {
            $array[key($array1)] = current($array1);
            next($array1);
        }
        while(current($array2)) {
            $array[key($array2)] = current($array2);
            next($array2);
        }
        return;
    }
}
