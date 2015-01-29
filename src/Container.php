<?php
namespace Iono\Container;

use Iono\Container\Contracts\ContainerInterface;

/**
 * Class Container
 * @package Iono\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Container implements ContainerInterface
{


    protected $bindings = [];

    /**
     * @param $context
     * @param array $parameters
     */
    public function newInstance($context, $parameters = [])
    {

    }
}
