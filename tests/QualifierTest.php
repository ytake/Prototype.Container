<?php

use Iono\Proto\Container\Container;

class QualifierTest extends \PHPUnit_Framework_TestCase
{

    /** @var Container */
    protected $container;

    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testSingletonQualifier()
    {
        $this->container->singleton("Resolvable", "ResolvePatternOne")->component('testing');
        $this->container->register("Resolvable",'ResolvePatternTwo')->component('contextual');
    }
}

/**
 * Interface ResolveInterface
 */
interface Resolvable { }

/**
 * Class ResolvePatternOne
 */
class ResolvePatternOne implements Resolvable {

    public $param = 0;
}

/**
 * Class ResolvePatternTwo
 */
class ResolvePatternTwo implements Resolvable {

    public $param = 1;
}
