<?php
namespace Iono\Container;

use ArrayAccess;
use ReflectionClass;
use Iono\Container\Contracts\ContainerInterface;

/**
 * Class Container
 * @package Iono\Container
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
     * @return mixed|object
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
            $dependencies = $this->resolveDependencies($reflectionClass, $parameters);

            if (isset($this->shares[$abstract])) {
                return $this->resolveSingleton($reflectionClass, $abstract, $dependencies);
            }
            $resolveInstance = $reflectionClass->newInstanceArgs($dependencies);

        } catch (\Exception $exception) {
            $resolveInstance = $abstract;
        }
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

                    if (isset($this->parameters[$reflectionClass->getName()][$constructorParameter->getName()])) {
                        $resolved[$constructorParameter->getName()]
                            = $this->parameters[$reflectionClass->getName()][$constructorParameter->getName()];
                    }

                    if (isset($parameters[$constructorParameter->getName()])) {
                        $resolved[$constructorParameter->getName()]
                            = $parameters[$constructorParameter->getName()];
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return object
     */
    public function offsetGet($offset)
    {
        return $this->newInstance($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }


}
