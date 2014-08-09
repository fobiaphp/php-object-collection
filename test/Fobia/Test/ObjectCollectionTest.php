<?php
/**
 * PHP Object Collection
 *
 * @author      Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright   Copyright (c) 2014 Dmitriy Tyurin
 * @package     Fobia
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Fobia\Test;

use Fobia\ObjectCollection;

class ObjectItem
{
    public $name;
    public $key;

    function __construct($name = 'default', $key = null, $keyName = null, $keyValue = null)
    {
        $this->name = $name;
        $this->key = $key;

        if ($keyName !== null) {
            $this->$keyName = $keyValue;
        }

    }

}

class ObjectCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Fobia\ObjectCollection
     */
    protected $object;

    public function newItem($key1)
    {
        $arr = array(
            'key1' => $key1
        );
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $k => $v) {
            $k = $k +2;
            $k = 'key'. $k;
            $arr[$k] = $v;
        }
        return $arr;
    }


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ObjectCollection(array(new ObjectItem()));
    }

    /**
     * @covers Fobia\ObjectCollection::eq
     * @todo   Implement testEq().
     */
    public function testEq()
    {
        $obj = $this->object->eq();
        $this->assertEquals('default', $obj->name);
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAt().
     */
    public function testAddAtDefault()
    {
        $obj = new ObjectItem('new');
        $this->object->addAt($obj);

        $this->assertEquals(2, $this->object->count());
        $this->assertEquals($obj, $this->object->eq(1));
    }

        /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAt().
     */
    public function testAddAtFirst()
    {
        $obj = new ObjectItem('new');
        $this->object->addAt($obj, 0);

        $this->assertEquals(2, $this->object->count());
        $this->assertEquals($obj, $this->object->eq(0));
    }

    /**
     * @covers Fobia\ObjectCollection::addAt
     * @todo   Implement testAddAt().
     */
    public function testAddAtOther()
    {
        $obj = new ObjectItem('new');
        $this->object->addAt($obj, 7);

        $this->assertEquals(2, $this->object->count());
        $this->assertEquals($obj, $this->object->eq(1));

        $obj2 = new ObjectItem('new 2');
        $this->object->addAt($obj2, 1);

        $this->assertEquals(3, $this->object->count());
        $this->assertEquals($obj2, $this->object->eq(1));

        $this->object->addAt($obj2, 1);
        $this->assertEquals(4, $this->object->count());
        $this->assertEquals($this->object->eq(2), $this->object->eq(1));
    }

    /**
     * @covers Fobia\ObjectCollection::find
     * @todo   Implement testFind().
     */
    public function testFindProperty()
    {
        $this->object->addAt(new ObjectItem('new_1'));
        $this->object->addAt(new ObjectItem('new_2'));
        $this->object->addAt(new ObjectItem('new_3'));

        $obj = new ObjectItem('other');
        $obj->otherKey = 17;
        $this->object->addAt($obj);

        $this->assertCount(5, $this->object);

        $resultFind = $this->object->find('otherKey');
        $this->assertInstanceOf('\Fobia\ObjectCollection', $resultFind);
        $this->assertCount(1, $resultFind);

        $this->assertEquals($obj, $resultFind->eq());
    }


    public function testFindValue()
    {
        $this->object->addAt(new ObjectItem('new_1', 1));
        $this->object->addAt(new ObjectItem('new_1', 2));
        $this->object->addAt(new ObjectItem('new_2', 3));
        $this->object->addAt(new ObjectItem('new_3', 4));

        $resultFind = $this->object->find('name', 'new_1');
        $this->assertCount(2, $resultFind);
        $this->assertNotSame($resultFind->eq(0), $resultFind->eq(1));
        
        $this->assertEquals($this->object->eq(1), $resultFind->eq(0));
        $this->assertEquals($this->object->eq(2), $resultFind->eq(1));


        $resultFind->set('key', 'find');
        $this->assertEquals('find', $this->object->eq(1)->key);
        $this->assertEquals('find', $this->object->eq(2)->key);
    }


    /**
     * @covers Fobia\ObjectCollection::set
     * @todo   Implement testSet().
     */
    public function testSet()
    {
        // Remove the following lines when you implement this test.
        $this->object->addAt(new ObjectItem());

        $this->object->set('key', 'set value');
        foreach ($this->object as $obj) {
            $this->assertEquals('set value', $obj->key);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::get
     * @todo   Implement testGet().
     */
    public function testGet()
    {
        $obj1 = new ObjectItem(1);
        $obj2 = new ObjectItem(2);
        $obj3 = new ObjectItem(3);

        $this->object->eq()->name = 0;
        $this->object->addAt($obj1);
        $this->object->addAt($obj2);
        $this->object->addAt($obj3);

        $get = $this->object->get('name');
        foreach ($get as $key => $value) {
            $this->assertEquals($key, $value);
        }
    }



    /**
     * @covers Fobia\ObjectCollection::merge
     * @todo   Implement testMerge().
     */
//    public function testMerge()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\ObjectCollection::removeAt
     * @todo   Implement testRemoveAt().
     */
    public function testRemoveAt()
    {
        $foo = new ObjectItem('foo');
        $this->object->addAt($foo);
        $this->assertCount(2, $this->object);

        $this->object->removeAt();
        $this->assertCount(1, $this->object);
        $this->assertNotEquals('foo', $this->object->eq()->name);

        $this->object->addAt($foo);
        $this->object->removeAt(0);
        $this->assertCount(1, $this->object);
        $this->assertEquals('foo', $this->object->eq()->name);
    }

    /**
     * @covers Fobia\ObjectCollection::remove
     * @todo   Implement testRemove().
     */
    public function testRemove()
    {
        $foo = new ObjectItem('foo');
        $this->object->addAt($foo);

        $this->object->remove($foo);
        $this->assertCount(1, $this->object);
        $this->assertNotEquals('foo', $this->object->eq()->name);
    }

    /**
     * @covers Fobia\ObjectCollection::each
     * @todo   Implement testEach().
     */
    public function testEach()
    {
        $this->object->addAt(new ObjectItem('new_4', 1));
        $this->object->addAt(new ObjectItem('new_3', 2));
        $this->object->addAt(new ObjectItem('new_2', 3));
        $this->object->addAt(new ObjectItem('new_1', 4));

        $self = & $this;
        $this->object->each(function($obj, $index) use ($self) {
            $self->assertEquals($self->object->eq($index), $obj);
            $obj->each = true;
        });

        foreach ($this->object as $obj) {
            $self->assertTrue($obj->each);
        }

        $findResult = $this->object->find('key', function($key) {
             return ($key > 2);
        });
        $self->assertCount(2, $findResult);
    }

    /**
     * @covers Fobia\ObjectCollection::sort
     * @todo   Implement testSort().
     */
    public function testSort()
    {
        $this->object->addAt(new ObjectItem('new_1', 4));
        $this->object->addAt(new ObjectItem('new_3', 2));
        $this->object->addAt(new ObjectItem('new_2', 3));
        $this->object->addAt(new ObjectItem('new_4', 1));
        $this->object->eq()->key = 1000;

        $this->object->sort('key');
        $this->assertEquals(1, $this->object->eq()->key);

        $this->object->sort(function($a, $b) {
            return ($a->key != 4);
        });
        $this->assertEquals(4, $this->object->eq()->key);
    }

    /**
     * @covers Fobia\ObjectCollection::count
     * @todo   Implement testCount().
     */
    public function testCount()
    {
        $this->assertCount(1, $this->object);
        $this->assertEquals(1, $this->object->count());
    }

    /**
     * @covers Fobia\ObjectCollection::getIterator
     * @todo   Implement testGetIterator().
     */
    public function testGetIterator()
    {
        $arr = $this->object->getIterator();
        $this->assertInternalType('object', $arr[0]);
        $this->assertInstanceOf('\\ArrayIterator', $arr);

        foreach ($this->object as $obj) {
            $this->assertEquals('default', $obj->name);
        }
    }

    /**
     * @covers Fobia\ObjectCollection::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
        $arr = $this->object->toArray();
        $this->assertInternalType('array', $arr);
        $this->assertInternalType('object', $arr[0]);
    }
}
