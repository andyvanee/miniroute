<?php

namespace MiniRoute;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase {
    protected function setUp() {
        $this->container = new Container;
    }

    public function testRegister() {
        $this->container->register('myservice', function() {
            return 'Hello World';
        });

        $this->assertEquals('Hello World', $this->container->myservice);
    }
}
