<?php

namespace MiniRoute;

class REPath {
    public function __construct(string $re) {
        $this->re = $re;
    }

    public function match($path) {
        $matches = null;
        preg_match($this->re, $path, $matches);
        return $matches;
    }

    public function __toString() {
        return $this->re;
    }
}
