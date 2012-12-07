<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Container;
use Geissler\CSL\Data\Data;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-04 at 23:02:28.
 */
class IsNumericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IsNumeric
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
     * @covers Geissler\CSL\Choose\IsNumeric::__construct
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": 5,
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::__construct
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate1()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "5",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate2()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "5th",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate3()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "D2",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate4()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "L2d",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate5()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "2, 3",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate6()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "2-4",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate7()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "2 & 4",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testValidate8()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "2nd",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertTrue($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testDoNotValidate()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "second",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertFalse($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testDoNotValidate1()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "2nd edition",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertFalse($this->object->validate());
    }

    /**
     * @covers Geissler\CSL\Choose\IsNumeric::validate
     */
    public function testDoNotValidate2()
    {
        $layout =   '<if is-numeric="edition">
                        <text value="TRUE"/>
                      </if>';
        $json = '[
    {
        "edition": "second",
        "id": "ITEM-1",
        "type": "book"
    }
]';
        $this->initElement($layout, $json);
        $this->assertFalse($this->object->validate());
    }

    protected function initElement($layout, $json)
    {
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        $xml = new \SimpleXMLElement($layout);
        $this->object   =   new IsNumeric($xml);
    }
}
