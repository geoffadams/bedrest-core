<?php

namespace BedRest\Tests\Rest;

use BedRest\Content\Negotiation\NegotiatedResult;
use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Rest\Request\Request;
use BedRest\Rest\Request\Type;
use BedRest\Rest\RestManager;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Tests\BaseTestCase;

/**
 * RestManagerTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RestManagerTest extends BaseTestCase
{
    /**
     * RestManager instance under test.
     *
     * @var \BedRest\Rest\RestManager
     */
    protected $restManager;

    /**
     * Mock BedRest\Rest\Configuration object.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    protected function setUp()
    {
        parent::setUp();

        $this->configuration = $this->getMock('BedRest\Rest\Configuration');
        $this->configuration
            ->expects($this->any())
            ->method('getContentTypes')
            ->will($this->returnValue(array('application/json')));

        $this->restManager = new RestManager($this->configuration);
    }

    /**
     * Gets a mock BedRest\Resource\Mapping\ResourceMetadataFactory object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @todo Remove usage of non-mock classes (ResourceMetadata for instance).
     */
    protected function getMockResourceMetadataFactory()
    {
        $testResourceMetadata = new ResourceMetadata('testResource');
        $testResourceMetadata->setName('testResource');
        $testResourceMetadata->setService('testService');

        $resourceMetadataFactory = $this->getMock(
            'BedRest\Resource\Mapping\ResourceMetadataFactory',
            array(),
            array(),
            '',
            false
        );

        $resourceMetadataFactory
            ->expects($this->any())
            ->method('getMetadataByResourceName')
            ->with($this->equalTo('testResource'))
            ->will($this->returnValue($testResourceMetadata));

        $resourceMetadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->with($this->equalTo('testResource'))
            ->will($this->returnValue($testResourceMetadata));

        return $resourceMetadataFactory;
    }

    protected function getMockServiceMetadataFactory()
    {
        $factory = $this->getMock(
            'BedRest\Service\Mapping\ServiceMetadataFactory',
            array(),
            array(),
            '',
            false
        );

        return $factory;
    }

    public function testConfiguration()
    {
        $config = $this->getMock('BedRest\Rest\Configuration');
        $restManager = new RestManager($config);

        $this->assertEquals($config, $restManager->getConfiguration());
    }

    public function testResourceMetadata()
    {
        $factory = $this->getMockResourceMetadataFactory();
        $this->restManager->setResourceMetadataFactory($factory);

        $meta = $factory->getMetadataFor('testResource');

        $this->assertEquals($meta, $this->restManager->getResourceMetadata($meta->getClassName()));
        $this->assertEquals($meta, $this->restManager->getResourceMetadataByName($meta->getName()));
    }

    public function testResourceMetadataFactory()
    {
        $factory = $this->getMockResourceMetadataFactory();
        $this->restManager->setResourceMetadataFactory($factory);

        $this->assertEquals($factory, $this->restManager->getResourceMetadataFactory());
    }

    public function testServiceMetadataFactory()
    {
        $factory = $this->getMockServiceMetadataFactory();
        $this->restManager->setServiceMetadataFactory($factory);

        $this->assertEquals($factory, $this->restManager->getServiceMetadataFactory());
    }

    public function testServiceLocator()
    {
        $locator = $this->getMock('BedRest\Service\LocatorInterface');
        $this->restManager->setServiceLocator($locator);

        $this->assertEquals($locator, $this->restManager->getServiceLocator());
    }

    public function testAppropriateServiceListenerCalled()
    {
        $this->restManager->setResourceMetadataFactory($this->getMockResourceMetadataFactory());

        // service metadata
        $serviceMetadata = new ServiceMetadata('testService');
        $serviceMetadata->setAllListeners(
            array(
                'GET'               => array('get'),
                'GET_COLLECTION'    => array('getCollection'),
                'POST'              => array('post'),
                'POST_COLLECTION'   => array('postCollection'),
                'PUT'               => array('put'),
                'PUT_COLLECTION'    => array('putCollection'),
                'DELETE'            => array('delete'),
                'DELETE_COLLECTION' => array('deleteCollection')
            )
        );

        $serviceMetadataFactory = $this->getMockServiceMetadataFactory();
        $serviceMetadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnValue($serviceMetadata));

        // service
        $service = $this->getMock('BedRest\TestFixtures\Services\Company\Generic');
        $service
            ->expects($this->once())
            ->method('get');

        $service
            ->expects($this->once())
            ->method('getCollection');

        $service
            ->expects($this->once())
            ->method('put');

        $service
            ->expects($this->once())
            ->method('putCollection');

        $service
            ->expects($this->once())
            ->method('post');

        $service
            ->expects($this->once())
            ->method('postCollection');

        $service
            ->expects($this->once())
            ->method('delete');

        $service
            ->expects($this->once())
            ->method('deleteCollection');

        // service locator
        $serviceLocator = $this->getMock('BedRest\Service\LocatorInterface');
        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('testService')
            ->will($this->returnValue($service));

        // negotiator
        $negotiator = $this->getMock('BedRest\Content\Negotiation\Negotiator');
        $negotiator
            ->expects($this->any())
            ->method('negotiate')
            ->will($this->returnValue(new NegotiatedResult()));

        // configure the RestManager
        $this->restManager->setServiceMetadataFactory($serviceMetadataFactory);
        $this->restManager->setServiceLocator($serviceLocator);
        $this->restManager->setContentNegotiator($negotiator);

        // form a basic request object, enough to get RestManager to process it correctly
        $request = new Request();
        $request->setAccept('application/json');
        $request->setResource('testResource');

        // test GET resource
        $request->setMethod(Type::METHOD_GET);
        $this->restManager->process($request);

        // test GET collection
        $request->setMethod(Type::METHOD_GET_COLLECTION);
        $this->restManager->process($request);

        // test POST resource
        $request->setMethod(Type::METHOD_POST);
        $this->restManager->process($request);

        // test POST collection
        $request->setMethod(Type::METHOD_POST_COLLECTION);
        $this->restManager->process($request);

        // test PUT resource
        $request->setMethod(Type::METHOD_PUT);
        $this->restManager->process($request);

        // test PUT collection
        $request->setMethod(Type::METHOD_PUT_COLLECTION);
        $this->restManager->process($request);

        // test DELETE resource
        $request->setMethod(Type::METHOD_DELETE);
        $this->restManager->process($request);

        // test DELETE collection
        $request->setMethod(Type::METHOD_DELETE_COLLECTION);
        $this->restManager->process($request);
    }
}
