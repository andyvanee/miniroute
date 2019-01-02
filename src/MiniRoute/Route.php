<?php

namespace MiniRoute;

class Route {
    public function __construct($path, $callback) {
        $this->path = $path;
        $this->callback = $callback;
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
