<?php

namespace MiniRoute;

class Route {
    public $path;

    public $callback;
    public $cls;
    public $fn;

    public function __construct($path, $callback, $cls, $fn) {
        $this->path = $path;
        $this->callback = $callback;
        $this->cls = $cls;
        $this->fn = $fn;
    }

    public function match($path) {
        if (is_string($this->path)) {
            return $this->path == $path;
        }
        return $this->path->match($path);
    }

    public function call() {
        return call_user_func_array($this->callback, func_get_args());
    }
}
