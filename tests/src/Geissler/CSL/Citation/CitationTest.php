<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-07 at 01:07:04.
 */
class CitationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Citation
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
     * @covers Geissler\CSL\Style\Citation::__construct
     */
    public function testInit()
    {
        $layout =   '<citation delimiter-precedes-last="always"/>';
        $this->initElement($layout);
        $this->assertEquals('always', Container::getContext()->getValue('delimiterPrecedesLast', 'citation'));
        Container::getContext()->setName('citation');
        $this->assertArrayHasKey('delimiterPrecedesLast', Container::getContext()->getOptions());
        $this->assertContains('always', Container::getContext()->getOptions());
    }

    /**
     * @covers Geissler\CSL\Style\Citation::render
     * @todo   Implement testRender().
     */
    public function testRender()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    protected function initElement($layout)
    {
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Citation($xml);
    }
}
