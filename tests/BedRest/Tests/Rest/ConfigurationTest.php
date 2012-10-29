<?php

namespace BedRest\Tests\Rest;

use BedRest\Tests\BaseTestCase;
use BedRest\Rest\Configuration;

/**
 * ConfigurationTest
 *
 * Tests BedRest\Rest\Configuration
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ConfigurationTest extends BaseTestCase
{
    public function testContentTypes()
    {
        $config = new Configuration();
        $contentTypes = array(
            'application/json',
            'text/xml'
        );

        $config->setContentTypes($contentTypes);

        $this->assertEquals($contentTypes, $config->getContentTypes());
    }

    public function testContentConverters()
    {
        $config = new Configuration();
        $converters = array(
            'application/json' => 'Test1',
            'text/xml' => 'Test2'
        );

        $config->setContentConverters($converters);

        $this->assertEquals($converters, $config->getContentConverters());

        $this->assertEquals('Test1', $config->getContentConverter('application/json'));
        $this->assertEquals('Test2', $config->getContentConverter('text/xml'));

        $this->assertNull($config->getContentConverter('text/plain'));
    }

    public function testDefaultResourceHandler()
    {
        $config = new Configuration();
        $config->setDefaultResourceHandler('ResourceHandlers\DefaultHandler');

        $this->assertEquals('ResourceHandlers\DefaultHandler', $config->getDefaultResourceHandler());
    }

    public function testDefaultService()
    {
        $config = new Configuration();
        $config->setDefaultService('Services\DefaultService');

        $this->assertEquals('Services\DefaultService', $config->getDefaultService());
    }

    public function testResourcePaths()
    {
        $config = new Configuration();

        $this->assertEmpty($config->getResourcePaths());

        $paths = array(
            'test1',
            'test2'
        );
        $config->setResourcePaths($paths);

        $this->assertEquals($paths, $config->getResourcePaths());
    }
}
