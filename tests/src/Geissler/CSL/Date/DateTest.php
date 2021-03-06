<?php

namespace Geissler\CSL\Date;

use Geissler\CSL\Factory;
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
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     */
    public function testRender()
    {
        $layout =   '<date prefix="(" suffix=")" variable="issued">
                        <date-part form="long" name="month" />
                        <date-part name="day" suffix=", " />
                        <date-part name="year" />
                      </date>';
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
        $this->initElement($layout, $json);

        $this->assertEquals('(499AD)', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     * @covers Geissler\CSL\Date\Date::hasAccessEmptyVariable
     */
    public function testRender1()
    {
        $layout =   '<date prefix="(" suffix=")" variable="issued">
        <date-part name="day" suffix=" " />
        <date-part form="long" name="month" suffix=" "/>
        <date-part name="year" />
      </date>';
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

        $this->initElement($layout, $json);

        $this->assertEquals('(August 1987–October 2003)', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     */
    public function testRenderStandardText()
    {
        $layout =   '<date variable="issued" form="text" date-parts="year-month-day"/>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    "1965",
                    "1",
                    "30"
                ]
            ]
        },
        "type": "book"
    }
]';

        $this->initElement($layout, $json);

        $this->assertEquals('January 30, 1965', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     */
    public function testRenderDateMonthShort()
    {
        $layout =   '<date date-parts="year-month" form="text" variable="issued">
        <date-part form="short" name="month" />
      </date>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    2005,
                    12,
                    15
                ]
            ]
        },
        "title": "Ignore me",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertEquals('Dec. 2005', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     */
    public function testRenderCustomDelimiter()
    {
        $layout =   '<date variable="issued">
                        <date-part name="day" suffix=" " range-delimiter="-"/>
                        <date-part name="month" suffix=" "/>
                        <date-part name="year" range-delimiter="/"/>
                      </date>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    "2008",
                    "5",
                    "1"
                ],
                [
                    "2008",
                    "5",
                    "4"
                ]
            ]
        },
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertEquals('1-4 May 2008', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     * @covers Geissler\CSL\Date\Date::partWithMaxDiff
     */
    public function testRenderCustomDelimiter1()
    {
        $layout =   '<date variable="issued">
                        <date-part name="day" suffix=" " range-delimiter="-"/>
                        <date-part name="month" suffix=" "/>
                        <date-part name="year" range-delimiter="/"/>
                      </date>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    "2008",
                    "5"
                ],
                [
                    "2009",
                    "6"
                ]
            ]
        },
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertEquals('May 2008/June 2009', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::formatDate
     * @covers Geissler\CSL\Date\Date::renderSeason
     */
    public function testRenderSeason()
    {
        $layout =   '<date variable="issued" prefix="(" suffix=")">
          <date-part name="month" />
          <date-part name="day" prefix=" " />
        </date>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    2000
                ]
            ],
            "season": 3
        },
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertEquals('(Autumn)', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::renderSeason
     */
    public function testRenderNoSeason()
    {
        $layout =   '<date variable="issued" prefix="(" suffix=")">
          <date-part name="month" />
          <date-part name="day" prefix=" " />
        </date>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    2000
                ]
            ]
        },
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertEquals('', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Date\Date::__construct
     * @covers Geissler\CSL\Date\Date::render
     * @covers Geissler\CSL\Date\Date::renderSeason
     * @covers Geissler\CSL\Date\Date::hasAccessEmptyVariable
     */
    public function testRenderNoSeason1()
    {
        $layout =   '<date variable="issued" prefix="(" suffix=")">
          <date-part name="day" prefix=" " />
        </date>';
        $json   =   '[
    {
        "id": "ITEM-1",
        "issued": {
            "date-parts": [
                [
                    2000
                ]
            ],
            "season": 3
        },
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertEquals('', $this->object->render(''));
        $this->assertTrue($this->object->hasAccessEmptyVariable());
    }

    protected function initElement($layout, $json, $language = 'en-US')
    {
        $locale = Factory::locale();
        $locale->readFile($language);
        Container::setLocale($locale);

        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Date($xml);
    }
}
