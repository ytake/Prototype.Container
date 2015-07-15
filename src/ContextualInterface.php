<?php

namespace Iono\Proto\Container;

/**
 * Interface ContextualInterface
 * @package Prototype\Container
 */
interface ContextualInterface
{

    /**
     * @param $name
     * @param $abstract
     */
    public function addComponent($name, $abstract);

    /**
     * @param $name
     * @return null|object
     */
    public function qualifier($name);
    
}
