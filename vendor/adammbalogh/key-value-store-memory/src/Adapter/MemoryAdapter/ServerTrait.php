<?php namespace AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;

trait ServerTrait
{
    /**
     * Removes all keys.
     *
     * @return void
     */
    public function flush()
    {
        $this->store = [];
    }
}
