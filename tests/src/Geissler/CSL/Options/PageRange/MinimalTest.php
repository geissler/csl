<?php
namespace Geissler\CSL\Options\PageRange;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-20 at 18:13:51.
 */
class MinimalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Minimal
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Minimal;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Minimal::format
     */
    public function testFormat()
    {
        $this->assertEquals('42–5', $this->object->format('42–45'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Minimal::format
     */
    public function testFormat1()
    {
        $this->assertEquals('142–5', $this->object->format('142–45'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Minimal::format
     */
    public function testFormat2()
    {
        $this->assertEquals('142–5', $this->object->format('142–5'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Minimal::format
     */
    public function testFormat3()
    {
        $this->assertEquals('1242–5', $this->object->format('1242–1245'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Minimal::format
     */
    public function testFormat4()
    {
        $this->assertEquals('1242–55', $this->object->format('1242–1255'));
    }

    /**
     * @covers Geissler\CSL\Options\PageRange\Minimal::format
     */
    public function testFormat5()
    {
        $this->assertEquals('1242–55', $this->object->format('1242–55'));
    }
}