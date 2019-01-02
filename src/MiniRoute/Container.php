<?php

namespace MiniRoute;

class Container {
    /**
     * Array of registered services (not resolved)
     */
    private $services = [];

    /**
     * Array of resolved services
     */
    private $resolvedServices = [];

    /**
     * Return a resolved service by calling it by name on the container.
     *
     *      $container = new MiniRoute\Container;
     *      $container->register('myservice', function() {
     *          return 'Hello World!';
     *      });
     *      echo $container->myservice;
     */
    public function __get($name) {
        if (!array_key_exists($name, $this->resolvedServices)) {
            $this->resolvedServices[$name] = $this->services[$name]($this);
        }
        return $this->resolvedServices[$name];
    }

    /**
     * Register a service for this container
     */
    public function register($name, $callback) {
        $this->services[$name] = $callback;
    }
}
