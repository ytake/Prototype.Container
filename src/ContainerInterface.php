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
 * Interface ContainerInterface
 *
 * @package Iono\ProtoType\Container
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
     * @param      $abstract
     * @param null $identifier
     * @return mixed
     */
    public function getParameters($abstract, $identifier = null);

    /**
     * @param      $abstract
     * @param null $identifier
     * @return mixed
     */
    public function getBinding($abstract, $identifier = null);
}
