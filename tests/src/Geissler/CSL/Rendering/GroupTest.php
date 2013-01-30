<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Data\Data;
use Geissler\CSL\Container;
use Geissler\CSL\Style\Citation;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-10 at 22:51:23.
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Group
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
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     */
    public function testRender()
    {
        $layout =   ' <group delimiter=" " prefix="(" suffix=")">
                        <text term="retrieved"/>
                        <text term="from"/>
                        <text variable="URL"/>
                      </group>';
        $json   =   '[{
                "URL": "http://dx.doi.org/10.1128/AEM.02591-07"
            }]';
        $this->initElement($layout, $json);
        Container::getLocale()->readFile();

        $this->assertEquals('(retrieved from http://dx.doi.org/10.1128/AEM.02591-07)', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     */
    public function testRender1()
    {
        $layout =   ' <group delimiter=" " prefix="(" suffix=")">
                        <text term="retrieved"/>
                        <text term="from"/>
                        <text variable="Link"/>
                        <text variable="URL"/>
                      </group>';
        $json   =   '[{
                "URL": "http://dx.doi.org/10.1128/AEM.02591-07"
            }]';
        $this->initElement($layout, $json);
        Container::getLocale()->readFile();

        $this->assertEquals('(retrieved from http://dx.doi.org/10.1128/AEM.02591-07)', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     */
    public function testRender2()
    {
        $layout =   ' <group delimiter=" " prefix="(" suffix=")">
                        <text term="retrieved"/>
                        <text term="from"/>
                      </group>';
        $json   =   '[{
                "URL": "http://dx.doi.org/10.1128/AEM.02591-07"
            }]';
        $this->initElement($layout, $json);
        Container::getLocale()->readFile();

        $this->assertEquals('(retrieved from)', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     */
    public function testRenderNames()
    {
        $layout =   '<group delimiter=", ">
                        <names variable="author">
                          <name and="symbol" delimiter=", " form="short" />
                        </names>
                      </group>';
        $json   =   '[
    {
        "author": [
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
        "issued": {
            "date-parts": [
                [
                    "2000"
                ]
            ]
        },
        "title": "Book A",
        "type": "book"
    },
    {
        "author": [
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
        "id": "ITEM-2",
        "issued": {
            "date-parts": [
                [
                    "2000"
                ]
            ]
        },
        "title": "Book B",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        Container::getLocale()->readFile();
        $citation   =   '<citation
                            disambiguate-add-givenname="true"
                            disambiguate-add-names="true"
                            disambiguate-add-year-suffix="true"
                            et-al-min="2"
                            et-al-use-first="1"
                            givenname-disambiguation-rule="by-cite">
                            <layout></layout>
                            </citation>';
        Container::setCitation(new Citation(new \SimpleXMLElement($citation)));
        Container::getContext()->setName('citation');

        $this->assertEquals('Doe et al.', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     */
    public function testRenderNumber()
    {
        $layout =   '<group delimiter=": ">
                        <text variable="title"/>
                        <number variable="edition" form="ordinal"/>
                        <label variable="edition"/>
                      </group>';
        $json   =   '[
                        {
                            "title": "Book A",
                            "edition": "1"
                        }
                    ]';
        $this->initElement($layout, $json);
        Container::getLocale()->readFile();

        $this->assertEquals('Book A: 1st: edition', $this->object->render(''));
    }

    /**
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     */
    public function testDoNotRender()
    {
        $layout =   ' <group delimiter=" ">
                        <text term="retrieved"/>
                        <text term="from"/>
                        <text variable="URL"/>
                      </group>';
        $json   =   '[{
                "no-URL": "http://aem.asm.org/content/74/9/2766"
            }]';
        $this->initElement($layout, $json);
        $this->assertEquals('', $this->object->render(''));
        $this->assertTrue($this->object->hasAccessEmptyVariable());
    }

    /**
     * @covers Geissler\CSL\Rendering\Group::__construct
     * @covers Geissler\CSL\Rendering\Group::render
     * @covers Geissler\CSL\Rendering\Group::hasAccessEmptyVariable
     * @covers Geissler\CSL\Rendering\Group::renderGroup
     */
    public function testRenderGroupInGroup()
    {
        $layout =   '<group delimiter=" ">
                        <text variable="title"/>
                        <group>
                            <text value="does not have title-short"/>
                        </group>
                    </group>';
        $json   =   '[{
                "no-URL": "http://aem.asm.org/content/74/9/2766"
            }]';
        $this->initElement($layout, $json);
        $this->assertEquals('does not have title-short', $this->object->render(''));
        $this->assertFalse($this->object->hasAccessEmptyVariable());
    }


    protected function initElement($layout, $json)
    {
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new Group($xml);
    }
}
