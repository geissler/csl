<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-02 at 18:41:58.
 */
class TextCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TextCase
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
     * @covers Geissler\CSL\Rendering\TextCase::__construct
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::keepNoCaseSpan
     */
    public function testRenderCapitalizeAll()
    {
        $this->initElement('<text variable="title" text-case="capitalize-all"/>');
        $this->assertEquals('This Is A Pen That Is A <span class="nocase">SMITH</span> Pencil', $this->object->render('This IS a Pen that is a <span class="nocase">SMITH</span> Pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::capitalizeFirst
     */
    public function testRenderCapitalizeFirst()
    {
        $this->initElement('<text variable="title" text-case="capitalize-first"/>');
        $this->assertEquals('This is a Pen that is a Smith Pencil', $this->object->render('this is a Pen that is a Smith Pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::capitalizeFirst
     */
    public function testRenderCapitalizeFirst1()
    {
        $this->initElement('<text variable="title" prefix="(" suffix=")" text-decoration="underline" text-case="capitalize-first"/>');
        $this->assertEquals('His book', $this->object->render('his book'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     */
    public function testRenderLowerCase()
    {
        $this->initElement('<text variable="title" text-case="lowercase"/>');
        $this->assertEquals('this is a pen that is a smith pencil', $this->object->render('This is a pen that is a Smith pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     */
    public function testRenderUpperCase()
    {
        $this->initElement('<text variable="title" text-case="uppercase"/>');
        $this->assertEquals('THIS IS A PEN THAT IS A SMITH PENCIL', $this->object->render('This is a pen that is a Smith pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderSentence
     * @covers Geissler\CSL\Rendering\TextCase::keepNoCaseSpan
     */
    public function testRenderSentence()
    {
        $this->initElement('<text variable="title" text-case="sentence"/>');
        $this->assertEquals('This is a pen that is a smith pencil', $this->object->render('THIS IS A PEN THAT IS A SMITH PENCIL'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderSentence
     * @covers Geissler\CSL\Rendering\TextCase::keepNoCaseSpan
     */
    public function testRenderSentence1()
    {
        $this->initElement('<text variable="title" text-case="sentence"/>');
        $this->assertEquals('This is a Pen that is a Smith Pencil', $this->object->render('this is a Pen that is a Smith Pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderTitle
     */
    public function testRenderTitle()
    {
        $locale = Factory::locale();
        $locale->readFile();
        Container::setLocale($locale);

        $this->initElement('<text variable="title" text-case="title"/>');
        $this->assertEquals('This IS a Pen That Is a Smith Pencil', $this->object->render('This IS a pen that is a Smith pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderTitle
     * @depends testRenderTitle
     */
    public function testRenderTitle1()
    {
        $this->initElement('<text variable="title" text-case="title"/>');
        $this->assertEquals('Review of Book by A.N. Author', $this->object->render('Review of Book by A.N. Author'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderTitle
     */
    public function testRenderTitle2()
    {
        $this->initElement('<text variable="title" text-case="title"/>');
        $this->assertEquals('Review of a Book by Me', $this->object->render('REVIEW OF A BOOK BY ME'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderTitle
     */
    public function testRenderTitle3()
    {
        $this->initElement('<text variable="title" text-case="title"/>');
        $this->assertEquals('This IS a Pen That Is a <span class=\"nocase\">smith</span> Pencil', $this->object->render('This IS a pen that is a <span class=\"nocase\">smith</span> pencil'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     * @covers Geissler\CSL\Rendering\TextCase::renderTitle
     * @depends testRenderTitle
     */
    public function testRenderTitleNotEnglish()
    {
        $locale = Factory::locale();
        $locale->readFile('de');
        Container::setLocale($locale);

        $this->initElement('<text variable="title" text-case="title"/>');
        $this->assertEquals('Review of book by A.N. author', $this->object->render('Review of book by A.N. author'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::render
     */
    public function testRenderNothing()
    {
        $locale = Factory::locale();
        $locale->readFile('de');
        Container::setLocale($locale);

        $this->initElement('<text variable="title"/>');
        $this->assertEquals('Review of book by A.N. author', $this->object->render('Review of book by A.N. author'));
    }

    /**
     * @covers Geissler\CSL\Rendering\TextCase::modify
     * @covers Geissler\CSL\Rendering\TextCase::render
     */
    public function testRenderModified()
    {
        $locale = Factory::locale();
        $locale->readFile('de');
        Container::setLocale($locale);

        $this->initElement('<text variable="title" text-case="sentence"/>');
        $xml = '<text variable="title" text-case="uppercase"/>';
        $this->assertInstanceOf('\Geissler\CSL\Rendering\TextCase', $this->object->modify(new \SimpleXMLElement($xml)));
        $this->assertEquals('THIS IS A PEN THAT IS A SMITH PENCIL', $this->object->render('this is a Pen that is a Smith Pencil'));
    }

    protected function initElement($layout)
    {
        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new TextCase($xml);
    }
}
