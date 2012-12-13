<?php
namespace Geissler\CSL\Data;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-12 at 20:13:33.
 */
class AbbreviationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Abbreviation
     */
    protected $object;
    protected $class = '\Geissler\CSL\Data\Abbreviation';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Abbreviation;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Data\Abbreviation::set
     */
    public function testSet()
    {
        $json = '{
    "default": {
        "container-title": {
            "Journal of Irreproducible Results": "J. Irrep. Res."
        }
    }
}';
        $this->assertInstanceOf($this->class, $this->object->set($json));
    }

    /**
     * @covers Geissler\CSL\Data\Abbreviation::set
     */
    public function testSetNot()
    {
        $json = '';
        $this->setExpectedException('ErrorException');
        $this->assertInstanceOf($this->class, $this->object->set($json));
    }

    /**
     * @covers Geissler\CSL\Data\Abbreviation::set
     * @covers Geissler\CSL\Data\Abbreviation::get
     */
    public function testGet()
    {
        $json = '{
    "default": {
        "container-title": {
            "Journal of Irreproducible Results": "J. Irrep. Res."
        }
    }
}';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('J. Irrep. Res.', $this->object->get('container-title'));
        $this->assertEquals('Journal of Irreproducible Results', $this->object->get('container-title', 'long'));
        $this->assertNull($this->object->get('title', 'long'));
    }
}