# Key Value Null Store

[![Author](http://img.shields.io/badge/author-@adammbalogh-blue.svg?style=flat)](https://twitter.com/adammbalogh)
[![Build Status](https://img.shields.io/travis/adammbalogh/key-value-store-null/master.svg?style=flat)](https://travis-ci.org/adammbalogh/key-value-store-null)
[![Coverage Status](https://img.shields.io/coveralls/adammbalogh/key-value-store-null.svg?style=flat)](https://coveralls.io/r/adammbalogh/key-value-store-null)
[![Quality Score](https://img.shields.io/scrutinizer/g/adammbalogh/key-value-store-null.svg?style=flat)](https://scrutinizer-ci.com/g/adammbalogh/key-value-store-null)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/adammbalogh/key-value-store-null.svg?style=flat)](https://packagist.org/packages/adammbalogh/key-value-store-null)
[![Total Downloads](https://img.shields.io/packagist/dt/adammbalogh/key-value-store-null.svg?style=flat)](https://packagist.org/packages/adammbalogh/key-value-store-null)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/203b92c4-a9bc-4d99-ba02-c20857bf593c/small.png)](https://insight.sensiolabs.com/projects/203b92c4-a9bc-4d99-ba02-c20857bf593c)

# Description

This library provides a layer to a key value null store.

It does not use a third party package. It literally does nothing.

Check out the [abstract library](https://github.com/adammbalogh/key-value-store) to see the other adapters and the Api.

# Installation

Install it through composer.

```json
{
    "require": {
        "adammbalogh/key-value-store-null": "@stable"
    }
}
```

**tip:** you should browse the [`adammbalogh/key-value-store-null`](https://packagist.org/packages/adammbalogh/key-value-store-null)
page to choose a stable version to use, avoid the `@stable` meta constraint.

# Usage

```php
<?php
use AdammBalogh\KeyValueStore\KeyValueStore;
use AdammBalogh\KeyValueStore\Exception\KeyNotFoundException;
use AdammBalogh\KeyValueStore\Adapter\NullAdapter as Adapter;

$adapter = new Adapter();

$kvs = new KeyValueStore($adapter);

$kvs->set('sample_key', 'Sample value');
try {
    $kvs->get('sample_key');
} catch (KeyNotFoundException $e) {
    // null adapter's get method throws KeyNotFoundException
}
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
