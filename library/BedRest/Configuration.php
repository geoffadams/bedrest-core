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

use BedRest\Mapping\Resource\Driver\Driver as ResourceDriver;
use BedRest\Mapping\Service\Driver\Driver as ServiceDriver;

/**
 * Configuration
 *
 * Configuration container for BedRest.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Configuration
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Resource metadata factory.
     * @var \BedRest\Mapping\Resource\ResourceMetadataFactory
     */
    protected $resourceMetadataFactory;
    
    /**
     * Resource metadata driver.
     * @var \BedRest\Mapping\Resource\Driver\Driver
     */
    protected $resourceMetadataDriverImpl;

    /**
     * Service metadata factory.
     * @var \BedRest\Mapping\Service\ServiceMetadataFactory
     */
    protected $serviceMetadataFactory;
    
    /**
     * Service metadata driver.
     * @var \BedRest\Mapping\Service\Driver\Driver
     */
    protected $serviceMetadataDriverImpl;

    /**
     * Array of service namespaces, analogous to Doctrine\ORM\Configuration's entity namespace storage.
     * @var array
     */
    protected $serviceNamespaces;

    /**
     * Returns the entity manager.
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Sets the entity manager.
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns all registered service namespace mappings.
     * @return array
     */
    public function getServiceNamespaces()
    {
        return $this->serviceNamespaces;
    }

    /**
     * Sets all service namespace mappings.
     * @param array $serviceNamespaces
     */
    public function setServiceNamespaces(array $serviceNamespaces)
    {
        $this->serviceNamespaces = $serviceNamespaces;
    }

    /**
     * Adds a single service namespace mapping.
     * @param string $alias
     * @param string $namespace
     */
    public function addServiceNamespace($alias, $namespace)
    {
        $this->serviceNamespaces[$alias] = $namespace;
    }

    /**
     * Sets the resource metadata driver implementation.
     * @param \BedRest\Mapping\Resource\Driver\Driver $driver
     */
    public function setResourceMetadataDriverImpl(ResourceDriver $driver)
    {
        $this->resourceMetadataDriverImpl = $driver;
    }

    /**
     * Returns the resource metadata driver implementation.
     * @return \BedRest\Mapping\Resource\Driver\Driver
     */
    public function getResourceMetadataDriverImpl()
    {
        return $this->resourceMetadataDriverImpl;
    }
    
    /**
     * Sets the service metadata driver implementation.
     * @param \BedRest\Mapping\Service\Driver\Driver $driver
     */
    public function setServiceMetadataDriverImpl(ServiceDriver $driver)
    {
        $this->serviceMetadataDriverImpl = $driver;
    }
    
    /**
     * Returns the service metadata driver implementation.
     * @return \BedRest\Mapping\Service\Driver\Driver
     */
    public function getServiceMetadataDriverImpl()
    {
        return $this->serviceMetadataDriverImpl;
    }
}

