<?php
namespace Geissler\CSL\Rendering;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-02 at 18:26:05.
 */
class StripPeriodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StripPeriods
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
     * @covers Geissler\CSL\Rendering\StripPeriods::render
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
        $this->object   =   new StripPeriods($xml);
    }
}
