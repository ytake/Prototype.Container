<?php
namespace Prototype\Container;

use ReflectionClass;
use Prototype\Container\Exception\InstantiableException;

/**
 * Class Container
 * @package Prototype\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Container implements ContainerInterface, ContextualInterface
{

    /** @var array */
    protected $bindings = [];

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $shares = [];

    /** @var array  */
    protected $component = [];

    /** @var */
    private static $instance;

    /**
     * get instance from container
     * @param $abstract
     * @param array $parameters
     * @return object
     */
    public function newInstance($abstract, array $parameters = [])
    {
        return $this->resolveInstance($abstract, $parameters);
    }

    /**
     * container binding
     * @param $abstract
     * @param $concrete
     * @param bool $singleton
     * @return Component
     */
    public function register($abstract, $concrete, $singleton = false)
    {
        $this->bindings[$abstract] = $concrete;
        if ($singleton) {
            $this->shares[$abstract] = true;
        }
        return new Component($this, $abstract);
    }

    /**
     * @param $abstract
     * @param $concrete
     */
    public function singleton($abstract, $concrete)
    {
        $this->register($abstract, $concrete, true);
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
        return $reflectionClass->newInstanceArgs($dependencies);
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
            $this->component = [];
        }
        unset($this->bindings[$abstract]);
        unset($this->parameters[$abstract]);
        unset($this->shares[$abstract]);
    }

    /**
     * @param $name
     * @param $abstract
     */
    public function addComponent($name, $abstract)
    {
        $this->component[$name][$abstract] = $this->bindings[$abstract];
    }

    /**
     * @param $name
     * @return null|object
     */
    public function qualifier($name)
    {
        if(isset($this->component[$name])) {
            foreach($this->component[$name] as $key => $bind) {
                return $this->newInstance($key);
            }
        }
        return null;
    }

}
