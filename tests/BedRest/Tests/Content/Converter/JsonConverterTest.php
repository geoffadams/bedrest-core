<?php

namespace BedRest\Tests\Content\Converter;

use BedRest\Content\Converter\JsonConverter;
use BedRest\Tests\BaseTestCase;

/**
 * JsonConverter
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonConverterTest extends BaseTestCase
{
    protected $decodedData = array(
        'one' => '1',
        'two' => '2',
        'three' => '3'
    );

    protected $encodedString = '{"one":"1","two":"2","three":"3"}';

    public function testInvalidJsonThrowsException()
    {
        $this->setExpectedException('BedRest\Content\Converter\Exception');

        $converter = new JsonConverter();

        $converter->decode('{not valid JSON');
    }

    public function testBasicEncode()
    {
        $converter = new JsonConverter();

        $json = $converter->encode($this->decodedData);

        $this->assertEquals($this->encodedString, $json);
    }

    public function testBasicDecode()
    {
        $converter = new JsonConverter();

        $object = $converter->decode($this->encodedString);

        $this->assertEquals($this->decodedData, $object);
    }
}
