<?php namespace AdammBalogh\KeyValueStore\Adapter;

use AdammBalogh\KeyValueStore\AbstractKvsMemoryTestCase;
use AdammBalogh\KeyValueStore\KeyValueStore;

class MemoryAdapterTest extends AbstractKvsMemoryTestCase
{
    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testInstantiation(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $this->assertNull($memoryAdapter->getClient());
    }
}
