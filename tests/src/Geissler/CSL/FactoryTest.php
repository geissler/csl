<?php
namespace Geissler\CSL;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-03 at 21:20:41.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Factory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Factory::locale
     * @covers Geissler\CSL\Factory::loadConfig
     */
    public function testLocale()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', Factory::locale());
    }

    /**
     * @covers Geissler\CSL\Factory::day
     */
    public function testDay()
    {
        $this->assertInstanceOf('\Geissler\CSL\Date\Day', Factory::day('text', new \SimpleXMLElement('<date-parte />')));
    }

    /**
     * @covers Geissler\CSL\Factory::month
     */
    public function testMonth()
    {
        $this->assertInstanceOf('\Geissler\CSL\Date\Month', Factory::month('text', new \SimpleXMLElement('<date-parte />')));
    }

    /**
     * @covers Geissler\CSL\Factory::year
     */
    public function testYear()
    {
        $this->assertInstanceOf('\Geissler\CSL\Date\Year', Factory::year('text', new \SimpleXMLElement('<date-parte />')));
    }
}
