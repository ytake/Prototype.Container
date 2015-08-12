<?php
/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Iono\Proto\Container;

use ReflectionClass;
use ReflectionParameter;
use Iono\Proto\Container\Exception\InstantiableException;

/**
 * Class Resolver
 * @package Iono\Proto\Container
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
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
     * @param       $abstract
     * @param array $parameters
     * @return mixed|null|object
     * @throws InstantiableException
     */
    public function makeInstance($abstract, array $parameters = [])
    {
        return $this->resolveInstance($abstract, $parameters);
    }

    /**
     * @param       $abstract
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
     * @param                 $abstract
     * @param array           $dependencies
     * @return mixed
     */
    protected function resolveSingleton(ReflectionClass $reflectionClass, $abstract, array $dependencies = [])
    {
        if (!isset(self::$instance[$abstract])) {
            self::$instance[$abstract] = $reflectionClass->newInstanceArgs($dependencies);
        }

        return self::$instance[$abstract];
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param array           $parameters
     * @return array
     * @throws InstantiableException
     */
    protected function resolveDependencies(ReflectionClass $reflectionClass, $parameters = [])
    {
        $resolved = [];

        if (!is_null($constructor = $reflectionClass->getConstructor())) {
            if ($constructorParameters = $constructor->getParameters()) {
                foreach ($constructorParameters as $constructorParameter) {

                    $resolved = $this->recursiveResolver($constructorParameter, $resolved);

                    $resolved = $this->resolveParameters($reflectionClass, $constructorParameter, $resolved);

                    if (isset($parameters[$constructorParameter->name])) {
                        $resolved[$constructorParameter->name] = $parameters[$constructorParameter->name];
                    }
                }
            }
        }

        return $resolved;
    }

    /**
     * @param ReflectionClass     $reflectionClass
     * @param ReflectionParameter $constructorParameter
     * @param array               $resolved
     * @return array
     */
    protected function resolveParameters(
        ReflectionClass $reflectionClass,
        ReflectionParameter $constructorParameter,
        array $resolved
    ) {
        if (isset($this->container->getParameters($reflectionClass->name)[$constructorParameter->name])) {
            $resolved[$constructorParameter->name]
                = $this->container->getParameters($reflectionClass->name)[$constructorParameter->name];

            return $resolved;
        }

        return $resolved;
    }

    /**
     * @param \ReflectionParameter $constructorParameter
     * @param array                $resolved
     * @return array
     * @throws InstantiableException
     */
    protected function recursiveResolver(ReflectionParameter $constructorParameter, array $resolved)
    {
        if ($constructorParameter->getClass()) {
            $resolved[] = $this->resolveInstance($constructorParameter->getClass()->name);

            return $resolved;
        }

        return $resolved;
    }
}
