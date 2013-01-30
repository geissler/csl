<?php
namespace Geissler\CSL\Names;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-09 at 16:11:38.
 */
class EtAlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EtAl
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
     * @covers Geissler\CSL\Names\EtAl::__construct
     * @covers Geissler\CSL\Names\EtAl::render
     */
    public function testRender()
    {
        $layout =   '<et-al />';
        $this->initElement($layout);

        $this->assertEquals(' et al.', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Names\EtAl::__construct
     * @covers Geissler\CSL\Names\EtAl::render
     */
    public function testRender1()
    {
        $layout =   '<et-al term="and others" font-style="italic"/>';
        $this->initElement($layout);

        $this->assertEquals('<font style="font-style:italic"> and others</font>', $this->object->render(''));
    }

    protected function initElement($layout)
    {
        \Geissler\CSL\Container::clear();
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new EtAl($xml);
    }
}
