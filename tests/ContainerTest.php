<?php

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Iono\Proto\Container\Container */
    protected $container;

    protected function setUp()
    {
        $this->container = new \Iono\Proto\Container\Container();
    }

    public function testBasicInstance()
    {
        $context = $this->container->newInstance("iono.container.tests");
        $this->assertSame("iono.container.tests", $context);

        $this->container->register("std.class", new \stdClass());
        $context = $this->container->newInstance("std.class");
        $this->assertInstanceOf("stdClass", $context);

        $this->container->register("ResolveInterface", "ResolveClass");
        $instance = $this->container->newInstance("ResolveInterface");
        $this->assertInstanceOf("ResolveClass", $instance);
        $instance->setValue("singleton");
        $this->assertSame("singleton", $instance->getValue());
        $instance = $this->container->newInstance("ResolveInterface");
        $this->assertNull($instance->getValue());

        $class = $this->container->newInstance("ResolveConstructor");
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());

        $class = $this->container->newInstance("ResolveConstructor", ["arg" => "dependency"]);
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());
        $this->assertSame("dependency", $class->getArg());
    }

    public function testSeparateInjectParamsInstance()
    {
        $this->container->register("ResolveInterface", "ResolveClass");
        $this->container->setParameters("ResolveConstructor", ["arg" => "dependency2"]);
        $class = $this->container->newInstance("ResolveConstructor");
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());
        $this->assertSame("dependency2", $class->getArg());

        $class = $this->container->newInstance("ResolveConstructor", ["arg" => "dependency3"]);
        $this->assertSame("dependency3", $class->getArg());
    }

    public function testGetInstance()
    {
        $this->container->register("ResolveInterface", "ResolveClass");
        $this->container->register("stdclass", "stdClass");
        $this->container->setParameters("ResolveConstructor", ["arg" => "dependency2"]);
        $class = $this->container->newInstance("ResolveConstructor");
        $this->assertInstanceOf("ResolveConstructor", $class);
        $this->assertInstanceOf("ResolveClass", $class->getInstance());
        $this->assertSame("dependency2", $class->getArg());

        $class = $this->container->newInstance("ResolveConstructor", ["arg" => new \stdClass()]);
        $this->assertInstanceOf("stdClass", $class->getArg());
        $class = $this->container->newInstance("ResolveConstructor", ["arg" => $this->container->newInstance("stdclass")]);
        $this->assertInstanceOf("stdClass", $class->getArg());
    }

    public function testGetClosure()
    {
        $this->container->register("closure", function () {
            return new \stdClass();
        });
        $this->assertInstanceOf('stdClass', $this->container->newInstance('closure'));
    }

    public function testSingletonInstance()
    {
        $this->container->singleton("ResolveInterface", "ResolveClass");
        $instance = $this->container->newInstance("ResolveInterface");
        $instance->setValue("singleton");
        $this->assertSame("singleton", $instance->getValue());
        $instance = $this->container->newInstance("ResolveInterface");
        $this->assertSame("singleton", $instance->getValue());
    }

    /**
     * @expectedException \Iono\Proto\Container\Exception\InstantiableException
     */
    public function testAbstractClass()
    {
        $this->container->newInstance("Resolver");
    }

    public function testAbstractResolveClass()
    {
        $this->container->register("AbstractResolver", "extendClass")->component('sample');
        $this->container->register("AbstractResolver",'contextualExtendClass')->component('contextual');
        $this->assertInstanceOf("Resolver", $this->container->newInstance("Resolver"));
        $this->assertInstanceOf("extendClass", $this->container->qualifier('sample'));
        $this->assertInstanceOf("contextualExtendClass", $this->container->qualifier('contextual'));

        $this->container->flushInstance('contextual');
        $this->assertNull($this->container->qualifier('contextual'));
        $this->container->flushInstance();
        $this->assertNull($this->container->qualifier('sample'));
    }

    public function testFlushContainer()
    {
        $this->container->register("AbstractResolver", "extendClass")->component('sample');
        $this->container->flushInstance();
        $this->assertNull($this->container->qualifier('sample'));
        $this->container->flushInstance('testing');
    }

    public function testNullReturnQualifier()
    {
        $this->assertNull($this->container->qualifier('sample'));
    }

    public function testBindingAccessor()
    {
        $this->container->register('abstract', 'accessor');
        $this->assertInternalType('array', $this->container->getBinding());
        $this->assertSame('accessor', $this->container->getBinding('abstract'));
    }

    public function testParameterAccessor()
    {
        $this->container->setParameters('abstract', ['param' => 'testing']);
        $this->assertInternalType('array', $this->container->getParameters());
        $this->assertNull($this->container->qualifier('abstract'));
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