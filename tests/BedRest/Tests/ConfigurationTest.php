<?php

namespace BedRest\Tests;

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
    public function testGetNullEntityManager()
    {
        $config = new Configuration();

        $this->assertEquals(null, $config->getEntityManager());
    }

    public function testSetEntityManager()
    {
        $config = new Configuration();

        $em = $this->getEntityManager();

        $config->setEntityManager($em);

        $this->assertEquals($em, $config->getEntityManager());
    }
}

