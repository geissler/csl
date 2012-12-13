<?php
namespace Geissler\CSL\Data;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-13 at 14:12:50.
 */
class CitationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Citation
     */
    protected $object;
    protected $class = 'Geissler\CSL\Data\Citation';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Citation;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     */
    public function testSet()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        }
    ],
    [
        {
            "id": "ITEM-2"
        }
    ],
    [
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     */
    public function testSet1()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        },
        {
            "id": "ITEM-2"
        },
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
    }

     /**
     * @covers Geissler\CSL\Data\Citation::set
     */
    public function testDoNotSet()
    {
        $json = '';
        $this->setExpectedException('ErrorException');
        $this->assertInstanceOf($this->class, $this->object->set($json));
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     * @covers Geissler\CSL\Data\Citation::get
     */
    public function testGet()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        },
        {
            "id": "ITEM-2"
        },
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('ITEM-1', $this->object->get('id'));
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     * @covers Geissler\CSL\Data\Citation::get
     */
    public function testGet1()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        }
    ],
    [
        {
            "id": "ITEM-2"
        }
    ],
    [
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('ITEM-1', $this->object->get('id'));
        $this->assertNull($this->object->get('prefix'));
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     * @covers Geissler\CSL\Data\Citation::get
     * @covers Geissler\CSL\Data\Citation::nextInGroup
     */
    public function testNextInGroup()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        }
    ],
    [
        {
            "id": "ITEM-2"
        }
    ],
    [
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('ITEM-1', $this->object->get('id'));
        $this->assertFalse($this->object->nextInGroup());
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     * @covers Geissler\CSL\Data\Citation::get
     * @covers Geissler\CSL\Data\Citation::nextInGroup
     */
    public function testNextInGroup1()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        },
        {
            "id": "ITEM-2"
        },
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('ITEM-1', $this->object->get('id'));
        $this->assertTrue($this->object->nextInGroup());
        $this->assertEquals('ITEM-2', $this->object->get('id'));
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     * @covers Geissler\CSL\Data\Citation::get
     * @covers Geissler\CSL\Data\Citation::next
     */
    public function testNext()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        },
        {
            "id": "ITEM-2"
        },
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('ITEM-1', $this->object->get('id'));
        $this->assertFalse($this->object->next());
    }

    /**
     * @covers Geissler\CSL\Data\Citation::set
     * @covers Geissler\CSL\Data\Citation::get
     * @covers Geissler\CSL\Data\Citation::next
     */
    public function testNext1()
    {
        $json = '[
    [
        {
            "id": "ITEM-1"
        }
    ],
    [
        {
            "id": "ITEM-2"
        }
    ],
    [
        {
            "id": "ITEM-3"
        }
    ]
]';
        $this->assertInstanceOf($this->class, $this->object->set($json));
        $this->assertEquals('ITEM-1', $this->object->get('id'));
        $this->assertTrue($this->object->next());
        $this->assertEquals('ITEM-2', $this->object->get('id'));
        $this->assertTrue($this->object->next());
        $this->assertEquals('ITEM-3', $this->object->get('id'));
        $this->assertFalse($this->object->next());
    }
}