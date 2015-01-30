<?php
namespace Iono\Container\Contracts;

/**
 * Interface ContainerInterface
 * @package Iono\Container\Contracts
 */
interface ContainerInterface 
{

    /**
     * @param $abstract
     * @param array $parameters
     * @return mixed
     */
    public function newInstance($abstract, array $parameters = []);

    /**
     * @param $abstract
     * @param $concrete
     * @param bool $singleton
     */
    public function binder($abstract, $concrete, $singleton = false);

    /**
     * @param $abstract
     * @param $concrete
     */
    public function singleton($abstract, $concrete);
    /**
     * @param $abstract
     * @param array $parameters
     * @return void
     */
    public function setParameters($abstract, array $parameters = []);

}
