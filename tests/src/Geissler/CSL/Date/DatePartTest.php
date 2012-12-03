<?php

namespace Geissler\CSL\Date;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-03 at 21:56:52.
 */
class DatePartTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DatePart
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
     * @covers Geissler\CSL\Rendering\DateParts::getRangeDelimiter
     * @todo   Implement testGetRangeDelimiter().
     */
    public function testGetRangeDelimiter()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Geissler\CSL\Date\DatePart::render
     */
    public function testRenderDay()
    {
        $layout =   '<date-part name="day" suffix=" " />';
        $this->initElement($layout);
        $this->assertEquals('23 ', $this->object->render(array('day' => 23, 'month' => 12, 'year' => 1984)));
    }

    /**
     * @covers Geissler\CSL\Date\DatePart::render
     */
    public function testRenderDay1()
    {
        $layout =   '<date-part name="day" suffix=". " form="numeric-leading-zeros"/>';
        $this->initElement($layout);
        $this->assertEquals('03. ', $this->object->render(array('day' => 3, 'month' => 12, 'year' => 1984)));
    }

    /**
     * @covers Geissler\CSL\Date\DatePart::render
     */
    public function testRenderDayAsOrdinal()
    {
        $locale = Factory::locale();
        $locale->readFile();
        Container::setLocale($locale);

        $layout =   '<date-part name="day" form="ordinal"/>';
        $this->initElement($layout);
        $this->assertEquals('1st', $this->object->render(array('day' => 1, 'month' => 12, 'year' => 1984)));
    }

     /**
     * @covers Geissler\CSL\Date\DatePart::render
     */
    public function testRenderDayAsOrdinal1()
    {
        $locale = Factory::locale();
        $locale->readFile();
        Container::setLocale($locale);

        $layout =   '<date-part name="day" form="ordinal"/>';
        $this->initElement($layout);
        $this->assertEquals('2nd', $this->object->render(array('day' => 2, 'month' => 12, 'year' => 1984)));
    }

    /**
     * @covers Geissler\CSL\Date\DatePart::render
     */
    public function testRenderDayAsFrenchOrdinal()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout =   '<date-part name="day" form="ordinal"/>';
        $this->initElement($layout);
        $this->assertEquals('1ʳᵉ', $this->object->render(array('day' => 1, 'month' => 12, 'year' => 1984)));
    }

    /**
     * @covers Geissler\CSL\Date\DatePart::render
     */
    public function testRenderDayAsFrenchOrdinal1()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout =   '<date-part name="day" form="ordinal"/>';
        $this->initElement($layout);
        $this->assertEquals('2', $this->object->render(array('day' => 2, 'month' => 12, 'year' => 1984)));
    }

    protected function initElement($layout)
    {
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new DatePart($xml);
    }
}
