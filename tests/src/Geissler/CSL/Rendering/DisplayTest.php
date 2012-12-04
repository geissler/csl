<?php
namespace Geissler\CSL\Rendering;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-02 at 17:57:08.
 */
class DisplayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Display
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
     * @covers Geissler\CSL\Rendering\Display::render
     * @covers Geissler\CSL\Rendering\Display::__construct
     */
    public function testRender()
    {
        $this->initElement('<text variable="note" display="block"/>');
        $this->assertEquals('<font style="display:block">Hello Word</font>', $this->object->render('Hello Word'));
    }

    /**
     * @covers Geissler\CSL\Rendering\Display::render
     */
    public function testRender1()
    {
        $this->initElement('<text variable="note" display="left-margin"/>');
        $this->assertEquals('Hello Word', $this->object->render('Hello Word'));
    }

    /**
     * @covers Geissler\CSL\Rendering\Display::render
     */
    public function testRender2()
    {
        $this->initElement('<text variable="note" display="right-inline"/>');
        $this->assertEquals('<font style="display:inline">Hello Word</font>', $this->object->render('Hello Word'));
    }

    /**
     * @covers Geissler\CSL\Rendering\Display::render
     */
    public function testRender3()
    {
        $this->initElement('<text variable="note" display="indent"/>');
        $this->assertEquals('<font style="text-indent: 0px; padding-left: 45px;">Hello Word</font>', $this->object->render('Hello Word'));
    }

    /**
     * @covers Geissler\CSL\Rendering\Display::render
     */
    public function testRender4()
    {
        $this->initElement('<text variable="note"/>');
        $this->assertEquals('Hello Word', $this->object->render('Hello Word'));
    }

    protected function initElement($layout)
    {
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Display($xml);
    }
}