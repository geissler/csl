<?php
namespace Geissler\CSL\Options\PageRange;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-20 at 17:35:22.
 */
class ExpandedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Expanded
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Expanded;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Expanded::format
     */
    public function testFormat()
    {
        $this->assertEquals('42–45', $this->object->format('42–5'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Expanded::format
     */
    public function testFormat1()
    {
        $this->assertEquals('42–45', $this->object->format('42–45'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Expanded::format
     */
    public function testFormat2()
    {
        $this->assertEquals('321–328', $this->object->format('321–8'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Expanded::format
     */
    public function testFormat3()
    {
        $this->assertEquals('321–328', $this->object->format('321–28'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Expanded::format
     */
    public function testFormat4()
    {
        $this->assertEquals('1321–1328', $this->object->format('1321–8'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Expanded::format
     */
    public function testFormat5()
    {
        $this->assertEquals('121–160', $this->object->format('121-160'));
    }
}
