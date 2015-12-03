<?php namespace AdammBalogh\KeyValueStore;

use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;

abstract class AbstractKvsMemoryTestCase extends \PHPUnit_Framework_TestCase
{
    public function kvsProvider()
    {
        $memoryAdapter = new MemoryAdapter(['key_param' => 666]);

        return [
            [
                new KeyValueStore($memoryAdapter),
                $memoryAdapter,
            ]
        ];
    }
}
