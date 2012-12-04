<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-02 at 22:17:03.
 */
class TermTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Term
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
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender()
    {
        $locale = Factory::locale();
        $locale->readFile();
        Container::setLocale($locale);

        $layout = '<text term="anonymous" form="short" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('anon.', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender1()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout = '<text term="anonymous" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('anonyme', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender2()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout = '<text term="edition" gender="feminine" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('édition', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender3()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout = '<text term="director" form="verb" gender="feminine" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('réalisé par', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender4()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout = '<text term="director" form="verb" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('réalisé par', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender5()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout = '<text term="thisisnoterm" form="verb" gender="feminine" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Term::render
     * @covers Geissler\CSL\Rendering\Term::__construct
     */
    public function testRender6()
    {
        $locale = Factory::locale();
        $locale->readFile('fr');
        Container::setLocale($locale);

        $layout = '<text term="paragraph" form="symbol" text-case="capitalize-first" strip-periods="true"/>';
        $this->initElement($layout);
        $this->assertEquals('§', $this->object->render(''));
    }

    protected function initElement($layout)
    {
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Term($xml);
    }
}