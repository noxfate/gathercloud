<?php namespace AdammBalogh\KeyValueStore\Adapter\NullAdapter;

use AdammBalogh\KeyValueStore\Adapter\NullAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class ValueTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AdammBalogh\KeyValueStore\Contract\AdapterInterface
     */
    protected $kvs;

    public function setUp()
    {
        $this->kvs = new KeyValueStore(new NullAdapter());
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\KeyNotFoundException
     */
    public function testGet()
    {
        $this->kvs->get('key');
    }

    public function testSet()
    {
        $this->assertFalse($this->kvs->set('key', 'value'));
    }

    public function tearDown()
    {
        unset($this->kvs);
    }
}
