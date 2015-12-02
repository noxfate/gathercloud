<?php namespace AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;

use AdammBalogh\KeyValueStore\AbstractKvsMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class ValueTraitTest extends AbstractKvsMemoryTestCase
{
    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testGet(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $this->assertEquals(555, $kvs->get('key'));
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\KeyNotFoundException
     *
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testGetKeyNotFound(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->get('key');
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testGetSerialized(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);
        $kvs->expire('key', 10);

        $this->assertEquals(555, $kvs->get('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testSet(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', [1,2,3]);

        $this->assertEquals([1,2,3], $kvs->get('key'));
    }
}
