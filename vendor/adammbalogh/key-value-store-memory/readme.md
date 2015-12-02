# Key Value Memory Store

[![Author](http://img.shields.io/badge/author-@adammbalogh-blue.svg?style=flat)](https://twitter.com/adammbalogh)
[![Build Status](https://img.shields.io/travis/adammbalogh/key-value-store-memory/master.svg?style=flat)](https://travis-ci.org/adammbalogh/key-value-store-memory)
[![Coverage Status](https://img.shields.io/coveralls/adammbalogh/key-value-store-memory.svg?style=flat)](https://coveralls.io/r/adammbalogh/key-value-store-memory)
[![Quality Score](https://img.shields.io/scrutinizer/g/adammbalogh/key-value-store-memory.svg?style=flat)](https://scrutinizer-ci.com/g/adammbalogh/key-value-store-memory)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/adammbalogh/key-value-store-memory.svg?style=flat)](https://packagist.org/packages/adammbalogh/key-value-store-memory)
[![Total Downloads](https://img.shields.io/packagist/dt/adammbalogh/key-value-store-memory.svg?style=flat)](https://packagist.org/packages/adammbalogh/key-value-store-memory)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/026ba4e0-0a39-4bd4-bc35-d4d7aac22994/small.png)](https://insight.sensiolabs.com/projects/026ba4e0-0a39-4bd4-bc35-d4d7aac22994)

# Description

This library provides a layer to a key value memory store.

It uses a simple php array.

Check out the [abstract library](https://github.com/adammbalogh/key-value-store) to see the other adapters and the Api.

# Installation

Install it through composer.

```json
{
    "require": {
        "adammbalogh/key-value-store-memory": "@stable"
    }
}
```

**tip:** you should browse the [`adammbalogh/key-value-store-memory`](https://packagist.org/packages/adammbalogh/key-value-store-memory)
page to choose a stable version to use, avoid the `@stable` meta constraint.

# Usage

```php
<?php
use AdammBalogh\KeyValueStore\KeyValueStore;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter as Adapter;

$adapter = new Adapter();

$kvs = new KeyValueStore($adapter);

$kvs->set('sample_key', 'Sample value');
$kvs->get('sample_key');
$kvs->delete('sample_key');
```

# API

**Please visit the [API](https://github.com/adammbalogh/key-value-store#api) link in the abstract library.**

# Toolset

| Key                 | Value               | Server           |
|------------------   |---------------------|------------------|
| ✔ delete            | ✔ get               | ✔ flush          |
| ✔ expire            | ✔ set               |                  |
| ✔ getTtl            |                     |                  |
| ✔ has               |                     |                  |
| ✔ persist           |                     |                  |

# Support

[![Support with Gittip](http://img.shields.io/gittip/adammbalogh.svg?style=flat)](https://www.gittip.com/adammbalogh/)
