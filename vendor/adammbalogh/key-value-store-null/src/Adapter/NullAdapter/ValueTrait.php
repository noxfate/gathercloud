<?php namespace AdammBalogh\KeyValueStore\Adapter\NullAdapter;

use AdammBalogh\KeyValueStore\Exception\KeyNotFoundException;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait ValueTrait
{
    /**
     * Gets the value of a key.
     *
     * @param string $key
     *
     * @throws KeyNotFoundException
     */
    public function get($key)
    {
        throw new KeyNotFoundException();
    }

    /**
     * Sets the value of a key.
     *
     * @param string $key
     * @param mixed $value Can be any of serializable data type.
     *
     * @return false
     */
    public function set($key, $value)
    {
        return false;
    }
}
