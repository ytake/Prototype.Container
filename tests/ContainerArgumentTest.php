<?php

class ContainerArgumentTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Iono\ProtoType\Container\Container */
    protected $container;

    protected function setUp()
    {
        $this->container = new Iono\ProtoType\Container\Container();
    }

    public function testArgumentContainer()
    {
        $this->container->setParameters('Testing', ['arg' => '1']);
        $this->container->setParameters('Testing', ['arg2' => '2']);
        $this->assertInstanceOf('Testing', $this->container->get('Testing'));
    }
}

class Testing
{
    protected $arg;

    protected $acme;

    /**
     * @param      $arg
     * @param Acme $acme
     */
    public function __construct($arg, Acme $acme)
    {
        $this->arg = $arg;
        $this->acme = $acme;
    }
}

class Acme
{
    //todo
}