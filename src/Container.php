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

use Interop\Container\ContainerInterface;

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

    /**
     * @param string $abstract
     * @return mixed|null|object
     */
    public function get($abstract)
    {
        return (new Resolver($this))->makeInstance($abstract);
    }

    /**
     * @param string $abstract
     * @return bool
     */
    public function has($abstract)
    {
        return (isset($this->bindings[$abstract])) ? true : false;
    }

    /**
     * @param          $abstract
     * @param          $concrete
     * @param bool|int $scope
     * @return $this
     */
    public function register($abstract, $concrete, $scope = Scope::PROTOTYPE)
    {
        $this->bindings[$abstract]['concrete'] = $concrete;
        $this->bindings[$abstract]['scope'] = $scope;

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
        if (isset($this->parameters[$abstract])) {
            $this->parameters[$abstract] = array_merge($this->parameters[$abstract], $parameters);

            return;
        }
        $this->parameters[$abstract] = $parameters;
    }

    /**
     * @param $abstract
     * @return null
     */
    public function getParameters($abstract)
    {
        return (isset($this->parameters[$abstract])) ? $this->parameters[$abstract] : null;
    }

    /**
     * @param $abstract
     * @return null
     */
    public function getBinding($abstract)
    {
        return (isset($this->bindings[$abstract]['concrete'])) ? $this->bindings[$abstract]['concrete'] : null;
    }

    /**
     * @param $abstract
     * @return null
     */
    public function getShare($abstract)
    {
        return (isset($this->bindings[$abstract]['scope'])) ? $this->bindings[$abstract]['scope'] : null;
    }

    /**
     * @param null $abstract
     */
    public function flush($abstract = null)
    {
        if (is_null($abstract)) {
            $this->bindings = [];
            $this->parameters = [];
            $this->shares = [];

            return;
        }
        unset($this->bindings[$abstract]);
        unset($this->parameters[$abstract]);
        unset($this->shares[$abstract]);
    }
}
