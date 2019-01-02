<?php

namespace MiniRoute;

class Response {
    public function __construct() {
        $this->_status = 200;
        $this->_body = '';
    }

    public function body($body = null) {
        if ($body) {
            $this->_body = $body;
        }
        return $this->_body;
    }

    public function status($code = null) {
        if ($code) {
            $this->_status = $code;
        }
        return $this->_status;
    }
}
