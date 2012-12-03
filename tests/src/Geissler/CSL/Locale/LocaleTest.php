<?php
namespace Geissler\CSL\Locale;

use Geissler\CSL\Factory;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-02 at 13:29:49.
 */
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Locale
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = Factory::locale();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::setDir
     */
    public function testSetDir()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->setDir('locales'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::setFile
     */
    public function testSetFile()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->setFile('locales-LANGAUGE.xml'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::setPrimaryDialect
     */
    public function testSetPrimaryDialect()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->setPrimaryDialect('{"de" : "de-DE"}'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::readFile
     */
    public function testReadFile()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::readXml
     * @todo   Implement testReadXml().
     */
    public function testReadXml()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getOptions
     */
    public function testGetOptions()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de'));
        $this->assertFalse($this->object->getOptions('punctuation-in-quote'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getDate
     * @todo   Implement testGetDate().
     */
    public function testGetDate()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile());
        $this->assertInternalType('array', $this->object->getDate('text'));
        $date   =   $this->object->getDate('text');
        $this->assertEquals('month', $date[0]['name']);
        $this->assertEquals('day', $date[1]['name']);
        $this->assertEquals('year', $date[2]['name']);
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetTerms()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('n. Chr.', $this->object->getTerms('ad'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetTerms1()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('¶', $this->object->getTerms('paragraph', 'symbol'));
        $this->assertEquals('¶¶', $this->object->getTerms('paragraph', 'symbol', 'multiple'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetTermsWithAdditional()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('fr'));
        $this->assertEquals('édition', $this->object->getTerms('edition', '', 'single', array('gender' => 'feminine')));
        $this->assertEquals('éditions', $this->object->getTerms('edition', '', 'multiple', array('gender' => 'feminine')));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetTermsWithAdditional1()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('fr'));
        $this->assertEquals('ʳᵉ', $this->object->getTerms('ordinal-01', '', 'single', array('gender-form' => 'feminine')));
        $this->assertEquals('ᵉʳ', $this->object->getTerms('ordinal-01', '', 'single', array('gender-form' => 'masculine')));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetMultipleTerms()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('Auflagen', $this->object->getTerms('edition', '', 'multiple'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetFormTerms()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('Aufl.', $this->object->getTerms('edition', 'short'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetFormAndMultipleTerms()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('Ref.', $this->object->getTerms('reference', 'short', 'multiple'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetFormAndMultipleTerms1()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('Ref.', $this->object->getTerms('reference', 'short'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetFormAndMultipleTerms2()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('Referenzen', $this->object->getTerms('reference', '', 'multiple'));
    }

    /**
     * @covers Geissler\CSL\Locale\Locale::getTerms
     */
    public function testGetFormAndMultipleTerms3()
    {
        $this->assertInstanceOf('\Geissler\CSL\Locale\Locale', $this->object->readFile('de-DE'));
        $this->assertEquals('Referenz', $this->object->getTerms('reference'));
    }
}
