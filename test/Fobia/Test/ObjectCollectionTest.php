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

    function __construct($name = 'default')
    {
        $this->name = $name;
    }

}

class ObjectCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectCollection
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
     * @covers Fobia\Base\ObjectCollection::eq
     * @todo   Implement testEq().
     */
    public function testEq()
    {
        $obj = $this->object->eq();
        $this->assertEquals('default', $obj->name);
    }

    /**
     * @covers Fobia\Base\ObjectCollection::find
     * @todo   Implement testFind().
     */
//    public function testFind()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::set
     * @todo   Implement testSet().
     */
//    public function testSet()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::get
     * @todo   Implement testGet().
     */
//    public function testGet()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::addAt
     * @todo   Implement testAddAt().
     */
    public function testAddAt()
    {
        $obj = new ObjectItem();
        $this->object->addAt($obj);

        $this->assertEquals(2, $this->object->count());
    }

    /**
     * @covers Fobia\Base\ObjectCollection::merge
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
     * @covers Fobia\Base\ObjectCollection::removeAt
     * @todo   Implement testRemoveAt().
     */
//    public function testRemoveAt()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::remove
     * @todo   Implement testRemove().
     */
//    public function testRemove()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::each
     * @todo   Implement testEach().
     */
//    public function testEach()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::sort
     * @todo   Implement testSort().
     */
//    public function testSort()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//    }

    /**
     * @covers Fobia\Base\ObjectCollection::count
     * @todo   Implement testCount().
     */
    public function testCount()
    {
        $this->assertEquals(1, $this->object->count());
    }

    /**
     * @covers Fobia\Base\ObjectCollection::getIterator
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
     * @covers Fobia\Base\ObjectCollection::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
        $arr = $this->object->toArray();
        $this->assertInternalType('array', $arr);
        $this->assertInternalType('object', $arr[0]);
    }
}
