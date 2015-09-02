<?php

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Iono\ProtoType\Container\Container */
    protected $container;

    protected function setUp()
    {
        $this->container = new Iono\ProtoType\Container\Container();
    }

    public function testBasicInstance()
    {
        $context = $this->container->get("iono.container.tests");
        $this->assertSame("iono.container.tests", $context);

        $this->container->register("std.class", new \stdClass());
        $context = $this->container->get("std.class");
        $this->assertInstanceOf("stdClass", $context);

        $this->container->register("ResolveInterface", "ResolveClass");
        $instance = $this->container->get("ResolveInterface");
        $this->assertInstanceOf("ResolveClass", $instance);

        $instance->setValue("singleton");
        $this->assertSame("singleton", $instance->getValue());

        $instance = $this->container->get("ResolveInterface");
        $this->assertNull($instance->getValue());

        $class = $this->container->get("ResolveConstructor");
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());
    }

    public function testSeparateInjectParamsInstance()
    {
        $this->container->register("ResolveInterface", "ResolveClass");
        $this->container->setParameters("ResolveConstructor", ["arg" => "dependency2"]);
        $class = $this->container->get("ResolveConstructor");
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());
        $this->assertSame("dependency2", $class->getArg());
    }

    public function testGetInstance()
    {
        $this->container->register("ResolveInterface", "ResolveClass");
        $this->container->register("stdclass", "stdClass");
        $this->container->setParameters("ResolveConstructor", ["arg" => "dependency2"]);
        $class = $this->container->get("ResolveConstructor");
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());
        $this->assertSame("dependency2", $class->getArg());
    }

    public function testGetClosure()
    {
        $this->container->register("closure", function () {
            return new \stdClass();
        });
        $this->assertInstanceOf('stdClass', $this->container->get('closure'));
    }

    public function testSingletonInstance()
    {
        $this->container->singleton("ResolveInterface", "ResolveClass");
        $instance = $this->container->get("ResolveInterface");
        $instance->setValue("singleton");
        $this->assertSame("singleton", $instance->getValue());

        $instance = $this->container->get("ResolveInterface");
        $this->assertSame("singleton", $instance->getValue());
    }

    /**
     * @expectedException \Iono\ProtoType\Container\Exception\InstantiableException
     */
    public function testAbstractClass()
    {
        $this->container->get("Resolver");
    }
}

/**
 * Interface ResolveInterface
 */
interface ResolveInterface
{
    public function getInstance();
}

/**
 * Class ResolveClass
 */
class ResolveClass implements ResolveInterface
{
    protected $value;
    public function getInstance()
    {
        return $this;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
    public function getValue()
    {
        return $this->value;
    }
}

class ResolveConstructor
{
    protected $resolve;
    protected $arg;
    public function __construct(ResolveInterface $resolve, $arg = null)
    {
        $this->resolve = $resolve;
        $this->arg = $arg;
    }
    public function getInstance()
    {
        return $this->resolve;
    }
    public function getArg()
    {
        return $this->arg;
    }
}


abstract class AbstractResolver
{

}

class extendClass extends AbstractResolver {

    public $param = 0;
}

class contextualExtendClass extends AbstractResolver {

    public $param = 1;
}

class Resolver
{
    public function __construct(AbstractResolver $class)
    {
        $this->class = $class;
    }
}