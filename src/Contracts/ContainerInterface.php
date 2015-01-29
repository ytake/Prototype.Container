<?php
namespace Iono\Container\Contracts;

/**
 * Interface ContainerInterface
 * @package Iono\Container\Contracts
 */
interface ContainerInterface 
{

    /**
     * @param $context
     * @param array $parameters
     * @return mixed
     */
    public function newInstance($context, $parameters = []);

}
