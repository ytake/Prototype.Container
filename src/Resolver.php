<?php
namespace Prototype\Container;

use ReflectionClass;
use Prototype\Container\Exception\InstantiableException;

/**
 * Class Resolver
 * @package Prototype\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Resolver
{

    /** @var ContainerInterface */
    protected $container;

    /** @var */
    private static $instance;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $abstract
     * @param array $parameters
     * @return mixed|null|object
     * @throws InstantiableException
     */
    public function makeInstance($abstract, array $parameters = [])
    {
        return $this->resolveInstance($abstract, $parameters);
    }

    /**
     * @param $abstract
     * @param array $parameters
     * @return mixed|null|object
     * @throws InstantiableException
     */
    protected function resolveInstance($abstract, array $parameters = [])
    {
        $concrete = (!is_null($this->container->getBinding($abstract))) ?
            $this->container->getBinding($abstract) : null;

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

        if ($this->container->getShare($abstract) === SCOPE::SINGLETON) {
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
     * @param array $parameters
     * @return array
     * @throws InstantiableException
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

                    if (isset($this->container->getParameters($reflectionClass->name)[$constructorParameter->name])) {
                        $resolved[$constructorParameter->name]
                            = $this->container->getParameters($reflectionClass->name)[$constructorParameter->name];
                    }

                    if (isset($parameters[$constructorParameter->name])) {
                        $resolved[$constructorParameter->name] = $parameters[$constructorParameter->name];
                    }
                }
            }
        }
        return $resolved;
    }

}
