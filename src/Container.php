<?php
namespace Prototype\Container;

use ArrayAccess;
use ReflectionClass;
use Prototype\Container\Contracts\ContainerInterface;
use Prototype\Container\Exception\InstantiableException;

/**
 * Class Container
 * @package Prototype\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Container implements ContainerInterface, ArrayAccess
{

    const PROTOTYPE = 1;

    const SINGLETON = 2;

    /** @var array */
    protected $bindings = [];

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $shares = [];

    /** @var */
    private static $instance;

    /**
     * @param $abstract
     * @param array $parameters
     * @return object
     */
    public function newInstance($abstract, array $parameters = [])
    {
        return $this->resolveInstance($abstract, $parameters);
    }

    /**
     * @param $abstract
     * @param $concrete
     * @param bool $singleton
     * @return $this
     */
    public function binder($abstract, $concrete, $singleton = false)
    {
        $this->bindings[$abstract] = $concrete;
        if ($singleton) {
            $this->shares[$abstract] = true;
        }
        return $this;
    }

    /**
     * @param $abstract
     * @param $concrete
     */
    public function singleton($abstract, $concrete)
    {
        $this->binder($abstract, $concrete, true);
    }


    public function contextual($name)
    {
        // @todo
    }

    /**
     * @param $abstract
     * @param array $parameters
     * @return void
     */
    public function setParameters($abstract, array $parameters = [])
    {
        $this->parameters[$abstract] = $parameters;
    }

    /**
     * @param $abstract
     * @param array $parameters
     * @return mixed|null|object
     * @throws InstantiableException
     */
    protected function resolveInstance($abstract, array $parameters = [])
    {
        $concrete = null;
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];
        }

        if ($concrete instanceof \Closure) {
            return call_user_func_array($concrete, $parameters);
        }

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        try {
            $reflectionClass = new ReflectionClass($concrete);
        } catch (\Exception $e) {
            return $concrete;
        }
        if (!$reflectionClass->isInstantiable()) {
            throw new InstantiableException("Errors");
        }

        $dependencies = $this->resolveDependencies($reflectionClass, $parameters);

        if (isset($this->shares[$abstract])) {
            return $this->resolveSingleton($reflectionClass, $abstract, $dependencies);
        }
        $resolveInstance = $reflectionClass->newInstanceArgs($dependencies);

        return $resolveInstance;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param $abstract
     * @param array $dependencies
     * @return mixed
     */
    protected function resolveSingleton(ReflectionClass $reflectionClass, $abstract, array $dependencies = [])
    {
        if (!self::$instance[$abstract]) {
            self::$instance[$abstract] = $reflectionClass->newInstanceArgs($dependencies);
        }
        return self::$instance[$abstract];

    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return array
     */
    protected function resolveDependencies(ReflectionClass $reflectionClass, $parameters = [])
    {
        $resolved = [];

        if (!is_null($constructor = $reflectionClass->getConstructor())) {
            if ($constructorParameters = $constructor->getParameters()) {
                foreach ($constructorParameters as $constructorParameter) {

                    if ($constructorParameter->getClass()) {
                        $resolved[] = $this->resolveInstance($constructorParameter->getClass()->name);
                    }

                    if (isset($this->parameters[$reflectionClass->name][$constructorParameter->name])) {
                        $resolved[$constructorParameter->name]
                            = $this->parameters[$reflectionClass->name][$constructorParameter->name];
                    }

                    if (isset($parameters[$constructorParameter->name])) {
                        $resolved[$constructorParameter->name] = $parameters[$constructorParameter->name];
                    }
                }
            }
        }
        return $resolved;
    }

    /**
     * @param null $abstract
     */
    public function flushInstance($abstract = null)
    {
        if(is_null($abstract)) {
            $this->bindings = [];
            $this->parameters = [];
            $this->shares = [];
        }
        unset($this->bindings[$abstract]);
        unset($this->parameters[$abstract]);
        unset($this->shares[$abstract]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->bindings[$offset]);
    }

    /**
     * @param mixed $offset
     * @return object
     */
    public function offsetGet($offset)
    {
        return $this->newInstance($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->bindings[$offset]);
    }


}
