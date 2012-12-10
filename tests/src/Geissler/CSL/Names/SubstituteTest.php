<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Factory;
use Geissler\CSL\Data\Data;
use Geissler\CSL\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-09 at 16:26:34.
 */
class SubstituteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Substitute
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
     * @covers Geissler\CSL\Names\Substitute::__construct
     * @covers Geissler\CSL\Names\Substitute::render
     */
    public function testRenderDate()
    {
        $layout =   '<substitute>
                        <date prefix="(" suffix=")" variable="issued">
                            <date-part name="day" suffix=" " />
                            <date-part form="long" name="month" suffix=" "/>
                            <date-part name="year" />
                        </date>
                        <number form="roman" variable="volume" />
                        <text variable="title"/>
                      </substitute>';
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
                "volume": "42",
                "title": "Ignore me",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('(August 1987–October 2003)', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Names\Substitute::__construct
     * @covers Geissler\CSL\Names\Substitute::render
     */
    public function testRenderText()
    {
        $layout =   '<substitute>
                        <date prefix="(" suffix=")" variable="issued">
                            <date-part name="day" suffix=" " />
                            <date-part form="long" name="month" suffix=" "/>
                            <date-part name="year" />
                        </date>
                        <number form="roman" variable="volume" />
                        <text variable="title"/>
                      </substitute>';
        $json   =   '[
            {
                "id": "ITEM-1",

                "title": "Ignore me",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('Ignore me', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Names\Substitute::__construct
     * @covers Geissler\CSL\Names\Substitute::render
     * @covers Geissler\CSL\Names\Substitute::hasAccessEmptyVariable
     */
    public function testRenderNumber()
    {
        $layout =   '<substitute>
                        <date prefix="(" suffix=")" variable="issued">
                            <date-part name="day" suffix=" " />
                            <date-part form="long" name="month" suffix=" "/>
                            <date-part name="year" />
                        </date>
                        <number form="roman" variable="volume" />
                        <text variable="title"/>
                      </substitute>';
        $json   =   '[
            {
                "id": "ITEM-1",

                "volume": "42",
                "title": "Ignore me",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('xlii', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Names\Substitute::__construct
     * @covers Geissler\CSL\Names\Substitute::render
     * @covers Geissler\CSL\Names\Substitute::hasAccessEmptyVariable
     */
    public function testRenderNames()
    {
        $layout =   '<substitute>
                        <names variable="editor"/>
                        <text macro="title"/>
                      </substitute>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "editor" : [
                    {
                        "family": "Doe",
                        "given": "John",
                        "static-ordering": false
                    }
                ],
                "volume": "42",
                "title": "Ignore me",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);
        Container::getContext()->setName('citation');
        $this->assertEquals('John Doe', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Names\Substitute::__construct
     * @covers Geissler\CSL\Names\Substitute::render
     * @covers Geissler\CSL\Names\Substitute::hasAccessEmptyVariable
     */
    public function testRenderNothing()
    {
        $layout =   '<substitute>
                        <date prefix="(" suffix=")" variable="issued">
                            <date-part name="day" suffix=" " />
                            <date-part form="long" name="month" suffix=" "/>
                            <date-part name="year" />
                        </date>
                        <number form="roman" variable="volume" />
                        <text variable="title"/>
                      </substitute>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('', $this->object->render(''));
        $this->assertTrue($this->object->hasAccessEmptyVariable());
    }

    protected function initElement($layout, $json, $language = 'en-US')
    {
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $locale = Factory::locale();
        $locale->readFile($language);
        Container::setLocale($locale);

        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Substitute($xml);
    }
}
