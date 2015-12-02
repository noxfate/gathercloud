<?php namespace AdammBalogh\KeyValueStore\Adapter;

use AdammBalogh\KeyValueStore\Adapter\NullAdapter\KeyTrait;
use AdammBalogh\KeyValueStore\Adapter\NullAdapter\ValueTrait;
use AdammBalogh\KeyValueStore\Adapter\NullAdapter\ServerTrait;

class NullAdapter extends AbstractAdapter
{
    use KeyTrait, ValueTrait, ServerTrait;

    /**
     * @var null
     */
    protected $client;

    /**
     * @return null
     */
    public function getClient()
    {
        return $this->client;
    }
}
