<?php

namespace Iono\Proto\Container;

/**
 * Interface ContainerInterface
 * @package Iono\Proto\Container
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
     * @param int $scope
     * @return mixed
     */
    public function register($abstract, $concrete, $scope = Scope::PROTOTYPE);

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

    /**
     * @param $abstract
     * @return mixed
     */
    public function getShare($abstract);

    /**
     * @param null $abstract
     * @return mixed
     */
    public function getParameters($abstract = null);

    /**
     * @param $abstract
     * @return null
     */
    public function getBinding($abstract = null);

}
