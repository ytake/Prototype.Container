<?php
namespace Prototype\Container;

/**
 * Class Component
 * @package Iono\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Component
{

    /** @var ContainerInterface  */
    protected $container;

    /** @var string */
    protected $abstract;

    /**
     * @param ContainerInterface $container
     * @param $abstract
     */
    public function __construct(ContainerInterface $container, $abstract)
    {
        $this->container = $container;
        $this->abstract = $abstract;
    }

    /**
     * @param $name
     * @return void
     */
    public function component($name)
    {
        if(method_exists($this->container, 'addComponent')) {
            $this->container->addComponent($name, $this->abstract);
        }
    }

}
