<?php

namespace Geissler\CSL\Date;

use Geissler\CSL\Data\Data;
use Geissler\CSL\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-03 at 21:35:30.
 */
class DateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Date
     */
    protected $object;

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Geissler\CSL\Rendering\Date::render
     */
    public function testRender()
    {
        $layout =   '<date prefix="(" suffix=")" variable="issued">
                        <date-part form="long" name="month" />
                        <date-part name="day" suffix=", " />
                        <date-part name="year" />
                      </date>';
        $this->initElement($layout);
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    499
                ]
            ]
        },
        "title": "Ignore me",
        "type": "book"
    }
]';
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $this->assertEquals('(499AD)', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Date::render
     */
    public function testRender1()
    {
        $layout =   '<date prefix="(" suffix=")" variable="issued">
        <date-part name="day" suffix=" " />
        <date-part form="long" name="month" suffix=" "/>
        <date-part name="year" />
      </date>';
        $this->initElement($layout);
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    1987,
                    8
                ],
                [
                    2003,
                    10
                ]
            ]
        },
        "title": "Ignore me",
        "type": "book"
    }
]';
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $this->assertEquals('(August 1987–October 2003)', $this->object->render(''));
    }

    protected function initElement($layout)
    {
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Date($xml);
    }
}
