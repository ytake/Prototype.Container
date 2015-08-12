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
 * Class Component
 *
 * @package Iono\Proto\Container
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Component
{
    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $abstract;

    /**
     * @param ContainerInterface $container
     * @param                    $abstract
     */
    public function __construct(ContainerInterface $container, $abstract)
    {
        $this->container = $container;
        $this->abstract = $abstract;
    }

    /**
     * @param $name
     * @return void
     */
    public function component($name)
    {
        if (method_exists($this->container, 'addComponent')) {
            $this->container->addComponent($name, $this->abstract);
        }
    }
}
