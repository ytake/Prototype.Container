<?php

use Iono\ProtoType\Container\Container;

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
        $this->container->identifier('testing')->singleton("Resolvable", "ResolvePatternOne");
        $this->container->identifier('contextual')->register("Resolvable", 'ResolvePatternTwo');

        $testing = $this->container->qualifier('testing');
        $testing->param = 100;
        $singletonTesting = $this->container->qualifier('testing');
        $this->assertSame(100, $singletonTesting->param);
        $contextual = $this->container->qualifier('contextual');
        $contextual->param = 100;
        $prototypeContextual = $this->container->qualifier('contextual');
        $this->assertSame(1, $prototypeContextual->param);
    }
}

/**
 * Interface ResolveInterface
 */
interface Resolvable
{
}

/**
 * Class ResolvePatternOne
 */
class ResolvePatternOne implements Resolvable
{

    public $param = 0;
}

/**
 * Class ResolvePatternTwo
 */
class ResolvePatternTwo implements Resolvable
{
    public $param = 1;
}
