<?php namespace AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;

use AdammBalogh\KeyValueStore\AbstractKvsMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class ServerTraitTest extends AbstractKvsMemoryTestCase
{
    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testFlush(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 5);

        $this->assertTrue($kvs->has('key'));
        $this->assertNull($kvs->flush());
        $this->assertFalse($kvs->has('key'));
    }
}
