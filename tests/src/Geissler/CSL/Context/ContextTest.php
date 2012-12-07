<?php
namespace Geissler\CSL\Context;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-06 at 19:07:31.
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $object;
    protected $class = '\Geissler\CSL\Context\Context';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Context;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Context\Context::__construct
     * @covers Geissler\CSL\Context\Context::setName
     * @covers Geissler\CSL\Context\Context::getName
     */
    public function testSetAndGetName()
    {
        $this->assertInstanceOf($this->class, $this->object->setName('citation'));
        $this->assertEquals('citation', $this->object->getName());
    }

    /**
     * @covers Geissler\CSL\Context\Context::getName
     */
    public function testGetName()
    {
        $this->assertNull($this->object->getName());
    }

    /**
     * @covers Geissler\CSL\Context\Context::addStyle
     * @covers Geissler\CSL\Context\Context::getValue
     */
    public function testAddStyle()
    {
        $this->assertInstanceOf($this->class, $this->object->addStyle('test', true));
        $this->assertTrue($this->object->getValue('test'));
    }

    /**
     * @covers Geissler\CSL\Context\Context::addCitation
     * @covers Geissler\CSL\Context\Context::getValue
     */
    public function testAddCitation()
    {
        $this->assertInstanceOf($this->class, $this->object->addCitation('testMe', 123));
        $this->assertEquals(123, $this->object->getValue('testMe', 'citation'));
    }

    /**
     * @covers Geissler\CSL\Context\Context::addBibliography
     * @covers Geissler\CSL\Context\Context::getValue
     */
    public function testAddBibliography()
    {
        $this->assertInstanceOf($this->class, $this->object->addBibliography('testMe', 'blub'));
        $this->assertEquals('blub', $this->object->getValue('testMe', 'bibliography'));
    }

    /**
     * @covers Geissler\CSL\Context\Context::getOptions
     */
    public function testGetOptions()
    {
        $this->assertInstanceOf($this->class, $this->object->setName('citation'));
        $this->assertInstanceOf($this->class, $this->object->addStyle('test', true));
        $this->assertInstanceOf($this->class, $this->object->addCitation('testMe', 123));
        $this->assertInternalType('array', $this->object->getOptions());
        $this->assertArrayHasKey('test', $this->object->getOptions());
        $this->assertArrayHasKey('testMe', $this->object->getOptions());
    }

    /**
     * @covers Geissler\CSL\Context\Context::getOptions
     */
    public function testGetOptions1()
    {
        $this->assertInstanceOf($this->class, $this->object->setName('bibliography'));
        $this->assertInstanceOf($this->class, $this->object->addStyle('test', true));
        $this->assertInstanceOf($this->class, $this->object->addCitation('testMe', 123));
        $this->assertInstanceOf($this->class, $this->object->addBibliography('testMore', 'blub'));
        $this->assertInternalType('array', $this->object->getOptions());
        $this->assertArrayHasKey('test', $this->object->getOptions());
        $this->assertArrayHasKey('testMore', $this->object->getOptions());
        $this->assertArrayNotHasKey('testMe', $this->object->getOptions());
    }

    /**
     * @covers Geissler\CSL\Context\Context::getOptions
     */
    public function testGetNoOptions()
    {
        $this->assertInstanceOf($this->class, $this->object->addStyle('test', true));
        $this->assertInstanceOf($this->class, $this->object->addCitation('testMe', 123));
        $this->assertInstanceOf($this->class, $this->object->addBibliography('testMore', 'blub'));
        $this->setExpectedException('ErrorException');
        $this->object->getOptions();
    }
}
