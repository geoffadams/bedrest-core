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

namespace BedRest;

use BedRest\Configuration;
use BedRest\EventManager;
use BedRest\RestManager;
use BedRest\Mapping\Service\ServiceMetadata;
use BedRest\Mapping\Service\ServiceMetadataFactory;

/**
 * ServiceManager
 *
 * Service manager for controlling instances of various services in use by
 * the system.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceManager
{
    /**
     * BedRest configuration.
     * @var \BedRest\Configuration
     */
    protected $configuration;

    /**
     * Stores all loaded service instances.
     * @var array
     */
    protected $loadedServices;
    
    /**
     * Service metadata factory.
     * @var \BedRest\Mapping\Service\ServiceMetadataFactory
     */
    protected $serviceMetadataFactory;

    /**
     * Event manager instance.
     * @var \BedRest\EventManager
     */
    protected $eventManager;
    
    /**
     * Constructor.
     * @param BedRest\Configuration $configuration
     * @param \BedRest\EventManager $eventManager
     */
    public function __construct(Configuration $configuration, EventManager $eventManager)
    {
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
        
        $this->serviceMetadataFactory = new ServiceMetadataFactory($configuration);
    }

    /**
     * Returns the configuration object.
     * @return \BedRest\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    /**
     * Returns the event manager.
     * @return \BedRest\EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
    
    /**
     * Returns service metadata for a class.
     * @param string $className
     * @return \BedRest\Mapping\Service\ServiceMetadata
     */
    public function getServiceMetadata($className)
    {
        return $this->serviceMetadataFactory->getMetadataFor($className);
    }
    
    /**
     * Returns the service metadata factory.
     * @return \BedRest\Mapping\Service\ServiceMetadataFactory
     */
    public function getServiceMetadataFactory()
    {
        return $this->serviceMetadataFactory;
    }

    /**
     * Returns an instance of the specified service.
     * @param string $className
     * @param \BedRest\RestManager $restManager
     * @param string $resourceClassName
     * @return object
     */
    public function getService($className, RestManager $restManager, $resourceClassName)
    {
        $hash = $this->getServiceHash($restManager, $resourceClassName);
        
        if (!isset($this->loadedServices[$className][$hash])) {
            $this->loadService($className, $restManager, $resourceClassName);
        }

        return $this->loadedServices[$className][$hash];
    }

    /**
     * Whether a service has been loaded or not yet.
     * @param string $className
     * @param string $resourceClassName
     * @return boolean
     */
    public function hasService($className, RestManager $restManager, $resourceClassName)
    {
        $hash = $this->getServiceHash($restManager, $resourceClassName);
        
        if (isset($this->loadedServices[$className][$hash])) {
            return true;
        }

        return false;
    }

    /**
     * Loads the specified service class.
     * @param string $className
     * @param \BedRest\RestManager $restManager
     * @param string $resourceClassName
     * @throws \Exception
     */
    protected function loadService($className, RestManager $restManager, $resourceClassName)
    {
        if (!class_exists($className)) {
            throw new Exception("Service '$className' not found.");
        }

        // instantiate the class
        $service = new $className(
            $restManager,
            $restManager->getResourceMetadata($resourceClassName)
        );
        
        // register events
        $this->registerServiceEvents($service);

        // store it locally for future reference
        $hash = $this->getServiceHash($restManager, $resourceClassName);
        $this->loadedServices[$className][$hash] = $service;
    }
    
    /**
     * Registers all events with the event manager for a particular service instance.
     * @param object $service
     */
    protected function registerServiceEvents($service)
    {
        $serviceMetadata = $this->serviceMetadataFactory->getMetadataFor(get_class($service));
        
        foreach ($serviceMetadata->getAllListeners() as $event => $observers) {
            foreach ($observers as $observer) {
                $this->eventManager->addListener($event, array($service, $observer));
            }
        }
    }
    
    /**
     * Gets the hash used for indexing loaded services.
     * @param \BedRest\RestManager $restManager
     * @param string $resourceClassName
     * @return string
     */
    protected function getServiceHash(RestManager $restManager, $resourceClassName)
    {
        return $hash = $resourceClassName . '#' . spl_object_hash($restManager);
    }
}

