# Data

A lightweight package that enables casting multidimensional arrays to objects and vice versa.

## Installation

You can install the package via composer:

```bash
composer require mementohub/data
```

## Usage

### Defining a Data Class

To define a data class, create a class that extends `Mementohub\Data\Data` and add properties to it.

```php
<?php

namespace App\Data;

use Mementohub\Data\Data;

class User extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $age,
    ) {}
}
```

### Parsing into Data

To parse an array into a data class, use the `from` method.

```php
$user = User::from([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
]);
```

### Transforming to Array

To transform a data class to an array, use the `toArray` method.

```php
$array = $user->toArray();
```


## Inspiration

* [spatie/laravel-data](https://github.com/spatie/laravel-data)
* [symfony/serializer](https://github.com/symfony/serializer)
