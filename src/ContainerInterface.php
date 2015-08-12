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

/**
 * Interface ContainerInterface
 *
 * @package Iono\Proto\Container
 */
interface ContainerInterface
{

    /**
     * @param       $abstract
     * @param array $parameters
     * @return mixed
     */
    public function newInstance($abstract, array $parameters = []);

    /**
     * @param     $abstract
     * @param     $concrete
     * @param int $scope
     * @return mixed
     */
    public function register($abstract, $concrete, $scope = Scope::PROTOTYPE);

    /**
     * @param $abstract
     * @param $concrete
     */
    public function singleton($abstract, $concrete);

    /**
     * @param       $abstract
     * @param array $parameters
     * @return void
     */
    public function setParameters($abstract, array $parameters = []);

    /**
     * @param $abstract
     * @return mixed
     */
    public function getShare($abstract);

    /**
     * @param null $abstract
     * @return mixed
     */
    public function getParameters($abstract = null);

    /**
     * @param $abstract
     * @return string[]
     */
    public function getBinding($abstract = null);
}
