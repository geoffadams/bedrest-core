<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace BedRest\Service\Mapping;

/**
 * ServiceMetadata
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadata
{
    /**
     * Class name of the service.
     * @var string
     */
    protected $className;

    /**
     * Event listeners for the service.
     * @var string
     */
    protected $listeners = array();

    /**
     * Class name of the data mapper.
     * @var string
     */
    protected $dataMapper;

    /**
     * Constructor.
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Sets the service class name.
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the service class name.
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Adds a listener for the specified event.
     * @param string $event
     * @param string $method
     */
    public function addListener($event, $method)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }

        $this->listeners[$event][] = $method;
    }

    /**
     * Returns the set of listeners for a specified event.
     * @param string $event
     * @return array
     */
    public function getListeners($event)
    {
        if (!isset($this->listeners[$event])) {
            return array();
        }

        return $this->listeners[$event];
    }

    /**
     * Sets all listeners, discarding the current set.
     * @param array $listeners
     */
    public function setAllListeners($listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * Returns the set of all listeners, indexed by event.
     * @return array
     */
    public function getAllListeners()
    {
        return $this->listeners;
    }

    /**
     * Sets the data mapper for this service.
     * @param string $dataMapper
     */
    public function setDataMapper($dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * Returns the data mapper class.
     * @return string
     */
    public function getDataMapper()
    {
        return $this->dataMapper;
    }
}
