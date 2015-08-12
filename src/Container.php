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

namespace Iono\ProtoType\Container;

/**
 * Class Container
 *
 * @package Iono\ProtoType\Container
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Container implements ContainerInterface
{
    /** @var array */
    protected $bindings = [];

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $shares = [];

    /** @var array */
    protected $component = [];

    /** @var string */
    protected $identifier;

    /**
     * get instance from container
     *
     * @param       $abstract
     * @param array $parameters
     * @return object
     */
    public function newInstance($abstract, array $parameters = [])
    {
        return (new Resolver($this))->makeInstance($abstract, $parameters);
    }

    /**
     * @param          $abstract
     * @param          $concrete
     * @param bool|int $scope
     * @return $this
     */
    public function register($abstract, $concrete, $scope = Scope::PROTOTYPE)
    {
        if (!is_null($this->identifier)) {
            $this->component[$this->identifier][$abstract] = [
                'concrete' => $concrete,
                'scope' => $scope
            ];

            return $this;
        }
        $this->bindings[$abstract]['concrete'] = $concrete;
        $this->bindings[$abstract]['scope'] = $scope;

        return $this;
    }

    /**
     * binding identifier
     *
     * @param $identifier
     * @return $this
     */
    public function identifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param $abstract
     * @param $concrete
     * @return $this
     */
    public function singleton($abstract, $concrete)
    {
        return $this->register($abstract, $concrete, Scope::SINGLETON);
    }

    /**
     * @param       $abstract
     * @param array $parameters
     * @return void
     */
    public function setParameters($abstract, array $parameters = [])
    {
        if (!is_null($this->identifier)) {
            $this->component[$this->identifier][$abstract]['parameters'] = $parameters;
        }
        $this->parameters[$abstract] = $parameters;
    }

    /**
     * @param      $abstract
     * @param null $identifier
     * @return null
     */
    public function getParameters($abstract, $identifier = null)
    {
        if (!is_null($identifier)) {
            return (isset($this->component[$identifier][$abstract]['parameters']))
                ? $this->component[$identifier][$abstract]['parameters'] : null;
        }

        return (isset($this->parameters[$abstract])) ? $this->parameters[$abstract] : null;
    }

    /**
     * @param null $abstract
     * @param null $identifier
     * @return array|null
     */
    public function getBinding($abstract, $identifier = null)
    {
        if (!is_null($identifier)) {
            return (isset($this->component[$identifier][$abstract]['concrete']))
                ? $this->component[$identifier][$abstract]['concrete'] : null;
        }

        return (isset($this->bindings[$abstract]['concrete'])) ? $this->bindings[$abstract]['concrete'] : null;
    }

    /**
     * @param $abstract
     * @return null
     */
    public function getShare($abstract, $identifier = null)
    {
        if (!is_null($identifier)) {
            return (isset($this->component[$identifier][$abstract]['scope']))
                ? $this->component[$identifier][$abstract]['scope'] : null;
        }

        return (isset($this->bindings[$abstract]['scope'])) ? $this->bindings[$abstract]['scope'] : null;
    }

    /**
     * @param null $abstract
     */
    public function flushInstance($abstract = null)
    {
        if (is_null($abstract)) {
            $this->bindings = [];
            $this->parameters = [];
            $this->shares = [];
            $this->component = [];

            return;
        }
        unset($this->bindings[$abstract]);
        unset($this->parameters[$abstract]);
        unset($this->shares[$abstract]);

        foreach ($this->component as $key => $bind) {
            unset($this->component[$key]);
        }
    }

    /**
     * use id, get instance from container
     *
     * @param $name
     * @return null|object
     */
    public function qualifier($name)
    {
        if (isset($this->component[$name])) {
            foreach ($this->component[$name] as $key => $bind) {
                $this->bindings[$key] = $bind;
                $instance = $this->newInstance($key);
                unset($this->bindings[$key]);

                return $instance;
            }
        }

        return null;
    }
}
