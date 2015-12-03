<?php namespace AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;

use AdammBalogh\KeyValueStore\AbstractKvsMemoryTestCase;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;
use AdammBalogh\KeyValueStore\KeyValueStore;

class KeyTraitTest extends AbstractKvsMemoryTestCase
{
    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testKeyFromParameter(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $this->assertTrue($kvs->has('key_param'));
        $this->assertEquals($kvs->get('key_param'), 666);
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testDelete(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $this->assertTrue($kvs->has('key'));
        $this->assertTrue($kvs->delete('key'));
        $this->assertFalse($kvs->has('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testDeleteFalse(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $this->assertFalse($kvs->delete('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testExpire(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $this->assertTrue($kvs->expire('key', 1));
        sleep(2);
        $this->assertFalse($kvs->has('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testExpireFalse(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $this->assertFalse($kvs->expire('key', 1));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testGetTtl(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $kvs->expire('key', 105);
        $this->assertEquals(105, $kvs->getTtl('key'));
    }

    /**
     * @expectedException \AdammBalogh\KeyValueStore\Exception\KeyNotFoundException
     *
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testGetTtlKeyNotFound(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->getTtl('key');
    }

    /**
     * @expectedException \Exception
     *
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testGetTtlError(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $this->assertEquals(105, $kvs->getTtl('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testPersist(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $kvs->expire('key', 105);
        $this->assertTrue($kvs->persist('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testPersistFalse(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $this->assertFalse($kvs->persist('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testPersistNoExpireSeconds(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $this->assertFalse($kvs->persist('key'));
    }

    /**
     * @dataProvider kvsProvider
     *
     * @param KeyValueStore $kvs
     * @param MemoryAdapter $memoryAdapter
     */
    public function testPersistExpired(KeyValueStore $kvs, MemoryAdapter $memoryAdapter)
    {
        $kvs->set('key', 555);

        $kvs->expire('key', 1);
        sleep(2);

        $this->assertFalse($kvs->persist('key'));
    }
}
