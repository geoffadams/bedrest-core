<?php

namespace BedRest\Tests\Rest;

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Rest\Dispatcher;
use BedRest\Rest\Request\Request;
use BedRest\Rest\Request\Type;
use BedRest\Service\Mapping\Annotation\Service;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Tests\BaseTestCase;

/**
 * DispatcherTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DispatcherTest extends BaseTestCase
{
    /**
     * RestManager instance under test.
     *
     * @var \BedRest\Rest\Dispatcher
     */
    protected $dispatcher;

    /**
     * @var \BedRest\Resource\Mapping\ResourceMetadata
     */
    protected $testResourceMeta;

    protected $testService;

    /**
     * @var \BedRest\Service\Mapping\ServiceMetadata
     */
    protected $testServiceMeta;

    protected $testSubService;

    /**
     * @var \BedRest\Service\Mapping\ServiceMetadata
     */
    protected $testSubServiceMeta;

    protected function setUp()
    {
        parent::setUp();

        $this->dispatcher = new Dispatcher();

        $this->testResourceMeta = new ResourceMetadata('testResource');
        $this->testResourceMeta->setName('testResource');
        $this->testResourceMeta->setService('testService');
        $this->testResourceMeta->setSubResources(
            array(
                'sub' => array(
                    'fieldName' => 'sub',
                    'service'   => 'testSubService',
                )
            )
        );

        $this->testService = $this->getMock(
            'BedRest\TestFixtures\Services\Company\Generic',
            array(),
            array(),
            'testService'
        );
        $this->testServiceMeta = new ServiceMetadata('testService');

        $this->testSubService = $this->getMock(
            'BedRest\TestFixtures\Services\Company\Generic',
            array(),
            array(),
            'testSubService'
        );
        $this->testSubServiceMeta = new ServiceMetadata('testSubService');
    }

    /**
     * Gets a mock BedRest\Resource\Mapping\ResourceMetadataFactory object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @todo Remove usage of non-mock classes (ResourceMetadata for instance).
     */
    protected function getMockResourceMetadataFactory()
    {
        $factory = $this->getMock(
            'BedRest\Resource\Mapping\ResourceMetadataFactory',
            array(),
            array(),
            '',
            false
        );

        // all tests operate on the testResource resource, anything else should throw an error
        $factory
            ->expects($this->any())
            ->method('getMetadataByResourceName')
            ->with('testResource')
            ->will($this->returnValue($this->testResourceMeta));

        $factory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->with('testResource')
            ->will($this->returnValue($this->testResourceMeta));

        return $factory;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockServiceLocator()
    {
        $locator = $this->getMock('BedRest\Service\LocatorInterface');
        $locator
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'getService')));

        return $locator;
    }

    public function getService($service)
    {
        switch ($service) {
            case 'testService':
                return $this->testService;
            case 'testSubService':
                return $this->testSubService;
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockServiceMetadataFactory()
    {
        $factory = $this->getMock(
            'BedRest\Service\Mapping\ServiceMetadataFactory',
            array(),
            array(),
            '',
            false
        );

        $factory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnCallback(array($this, 'getServiceMeta')));

        return $factory;
    }

    public function getServiceMeta($service)
    {
        switch ($service) {
            case 'testService':
                return $this->testServiceMeta;
            case 'testSubService':
                return $this->testSubServiceMeta;
        }
    }

    public function testEventManager()
    {
        $eventManager = $this->getMock('BedRest\Events\EventManager');

        $this->dispatcher->setEventManager($eventManager);
        $this->assertEquals($eventManager, $this->dispatcher->getEventManager());
    }

    public function testResourceMetadataFactory()
    {
        $factory = $this->getMockResourceMetadataFactory();

        $this->dispatcher->setResourceMetadataFactory($factory);
        $this->assertEquals($factory, $this->dispatcher->getResourceMetadataFactory());
    }

    public function testServiceMetadataFactory()
    {
        $factory = $this->getMockServiceMetadataFactory();

        $this->dispatcher->setServiceMetadataFactory($factory);
        $this->assertEquals($factory, $this->dispatcher->getServiceMetadataFactory());
    }

    public function testServiceLocator()
    {
        $locator = $this->getMock('BedRest\Service\LocatorInterface');

        $this->dispatcher->setServiceLocator($locator);
        $this->assertEquals($locator, $this->dispatcher->getServiceLocator());
    }

    public function requests()
    {
        return array(
            array(Type::METHOD_GET),
            array(Type::METHOD_GET_COLLECTION),
            array(Type::METHOD_POST),
            array(Type::METHOD_POST_COLLECTION),
            array(Type::METHOD_PUT),
            array(Type::METHOD_PUT_COLLECTION),
            array(Type::METHOD_DELETE),
            array(Type::METHOD_DELETE_COLLECTION),
        );
    }

    /**
     * @dataProvider requests
     *
     * @param string $method
     */
    public function testDispatchFiresCorrectEvent($method)
    {
        // configure the Dispatcher with the necessary dependencies
        $this->dispatcher->setResourceMetadataFactory($this->getMockResourceMetadataFactory());
        $this->dispatcher->setServiceMetadataFactory($this->getMockServiceMetadataFactory());
        $this->dispatcher->setServiceLocator($this->getMockServiceLocator());

        $eventManager = $this->getMock('BedRest\Events\EventManager');
        $eventManager
            ->expects($this->any())
            ->method('addListeners');
        $this->dispatcher->setEventManager($eventManager);

        // form a basic request
        $request = new Request();
        $request->setResource('testResource');
        $request->setMethod($method);

        // register listeners for the event
        $event = $method;
        $listener = strtolower($event) . 'Listener';

        $this->testServiceMeta->addListener($event, $listener);

        // test the correct event is fired
        $eventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with($event);

        $this->dispatcher->dispatch($request);
    }

    /**
     * @dataProvider requests
     *
     * @param string $method
     */
    public function testDispatchUsesSubResourceService($method)
    {
        // configure the Dispatcher with the necessary dependencies
        $this->dispatcher->setResourceMetadataFactory($this->getMockResourceMetadataFactory());
        $this->dispatcher->setServiceMetadataFactory($this->getMockServiceMetadataFactory($method));
        $this->dispatcher->setServiceLocator($this->getMockServiceLocator());

        $eventManager = $this->getMock('BedRest\Events\EventManager');
        $this->dispatcher->setEventManager($eventManager);

        // form a basic request
        $request = new Request();
        $request->setResource('testResource/sub');
        $request->setMethod($method);

        // register listeners for the event on both services
        $event = $method;
        $listener = strtolower($event) . 'Listener';

        $this->testServiceMeta->addListener($event, $listener);
        $this->testSubServiceMeta->addListener($event, $listener);

        // test the right service has its listeners registered
        $eventManager
            ->expects($this->any())
            ->method('addListeners')
            ->with($event, array($this->testSubService, $listener));

        $this->dispatcher->dispatch($request);
    }

    /**
     * @dataProvider requests
     *
     * @param string $method
     */
    public function testDispatchToNonExistentResourceThrows404Exception($method)
    {
        $resourceName = 'nonExistentResource';
        $notFoundException = \BedRest\Resource\Mapping\Exception::resourceNotFound($resourceName);

        // configure the Dispatcher with the necessary dependencies
        $rmdFactory = $this->getMockResourceMetadataFactory();
        $rmdFactory
            ->expects($this->any())
            ->method('getMetadataByResourceName')
            ->with($resourceName)
            ->will($this->throwException($notFoundException));

        $rmdFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->with($resourceName)
            ->will($this->throwException($notFoundException));

        $this->dispatcher->setResourceMetadataFactory($rmdFactory);
        $this->dispatcher->setServiceMetadataFactory($this->getMockServiceMetadataFactory($method));
        $this->dispatcher->setServiceLocator($this->getMockServiceLocator());

        $eventManager = $this->getMock('BedRest\Events\EventManager');
        $this->dispatcher->setEventManager($eventManager);

        // form a basic request
        $request = new Request();
        $request->setResource($resourceName);
        $request->setMethod($method);

        // register listeners for the event on both services
        $event = $method;
        $listener = strtolower($event) . 'Listener';

        $this->testServiceMeta->addListener($event, $listener);
        $this->testSubServiceMeta->addListener($event, $listener);

        // test an exception is thrown
        $this->setExpectedException('BedRest\Rest\Exception', '', 404);

        $this->dispatcher->dispatch($request);
    }

    /**
     * @dataProvider requests
     *
     * @param string $method
     */
    public function testDispatchToNonExistentSubResourceThrows404Exception($method)
    {
        // configure the Dispatcher with the necessary dependencies
        $this->dispatcher->setResourceMetadataFactory($this->getMockResourceMetadataFactory());
        $this->dispatcher->setServiceMetadataFactory($this->getMockServiceMetadataFactory($method));
        $this->dispatcher->setServiceLocator($this->getMockServiceLocator());

        $eventManager = $this->getMock('BedRest\Events\EventManager');
        $this->dispatcher->setEventManager($eventManager);

        // form a basic request
        $request = new Request();
        $request->setResource('testResource/nonExistentSub');
        $request->setMethod($method);

        // register listeners for the event on both services
        $event = $method;
        $listener = strtolower($event) . 'Listener';

        $this->testServiceMeta->addListener($event, $listener);
        $this->testSubServiceMeta->addListener($event, $listener);

        // test an exception is thrown
        $this->setExpectedException('BedRest\Rest\Exception', '', 404);

        $this->dispatcher->dispatch($request);
    }
}
