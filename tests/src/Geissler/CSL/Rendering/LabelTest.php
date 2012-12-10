<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;
use Geissler\CSL\Data\Data;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-09 at 17:20:50.
 */
class LabelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Label
     */
    protected $object;
    protected $class = '\Geissler\CSL\Rendering\Label';


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::setVariable
     */
    public function testSetVariable()
    {
        $layout =   '<label variable="page"/>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "page": "1-12",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);
        $this->assertInstanceOf('\Geissler\CSL\Rendering\Label', $this->object->setVariable('page'));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRender()
    {
        $layout =   '<label variable="page"/>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "page": "1-12",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('pages', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRenderAlwaysMultiple()
    {
        $layout =   '<label variable="page" plural="always"/>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "page": "1",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('pages', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRenderNeverMultiple()
    {
        $layout =   '<label variable="page" plural="never"/>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "page": "23-42",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('page', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRenderNeverMultiple1()
    {
        $layout =   '<label variable="page" plural="never" prefix="[" suffix="]"/>';
        $json   =   '[
            {
                "id": "ITEM-1",
                "page": "23-42",
                "type": "book"
            }
        ]';
        $this->initElement($layout, $json);

        $this->assertEquals('[page]', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::setVariable
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRenderShort()
    {
        $layout =   '<label form="short" plural="contextual"/>';
        $json   =   '[
    {
        "editor": [
            {
                "family": "Doe",
                "given": "John",
                "static-ordering": false
            },
            {
                "family": "Roe",
                "given": "Jane",
                "static-ordering": false
            }
        ],
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertInstanceOf($this->class, $this->object->setVariable('editor'));
        $this->assertEquals('eds.', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::setVariable
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRenderLong()
    {
        $layout =   '<label plural="contextual" strip-periods="true" />';
        $json   =   '[
    {
        "editor": [
            {
                "family": "Doe",
                "given": "John",
                "static-ordering": false
            }
        ],
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertInstanceOf($this->class, $this->object->setVariable('editor'));
        $this->assertEquals('editor', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Label::__construct
     * @covers Geissler\CSL\Rendering\Label::render
     */
    public function testRenderNot()
    {
        $layout =   '<label plural="contextual" strip-periods="true" />';
        $json   =   '[
    {
        "editor": [
            {
                "family": "Doe",
                "given": "John",
                "static-ordering": false
            }
        ],
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->setExpectedException('ErrorException');
        $this->object->render('');
    }


    protected function initElement($layout, $json, $language = 'en-US')
    {
        $locale = Factory::locale();
        $locale->readFile($language);
        Container::setLocale($locale);

        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Label($xml);
    }
}
