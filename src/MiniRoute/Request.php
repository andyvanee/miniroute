<?php

namespace MiniRoute;

class Request {
    public function __construct($args = null, $params = null) {
        $this->params = new \StdClass;
        if ($args) {
            $this->setArgs($args);
        } else {
            $this->setArgs($_SERVER);
        }
        if ($params) {
            $this->setParams($params);
        } else {
            $this->setParams($_REQUEST);
        }
        $this->setDefaults();
    }

    private function setDefaults() {
        $this->path = parse_url($this->REQUEST_URI, PHP_URL_PATH);
        $this->query = parse_url($this->REQUEST_URI, PHP_URL_QUERY);
        $this->method = $this->REQUEST_METHOD;
        $this->pathComponents = explode('/', trim($this->path, '/'));
    }

    private function setArgs(array $args) {
        foreach ($args as $k => $v) {
            $this->$k = $v;
        }
    }

    private function setParams(array $params) {
        foreach ($params as $k => $v) {
            $this->params->$k = $v;
        }
    }
}
