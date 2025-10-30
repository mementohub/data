<?php

namespace Mementohub\Data\Entities;

use Mementohub\Data\Values\Optional;
use ReflectionProperty;

/**
 * @mixin ReflectionProperty
 */
class DataProperty
{
    public function __construct(
        protected readonly ReflectionProperty $property
    ) {}

    public function allowsOptional(): bool
    {
        return $this->getType()->allows(Optional::class);
    }

    public function allowsNull(): bool
    {
        return $this->getType()->allowsNull();
    }

    public function needsParsing(): bool
    {
        return ! $this->getType()->isBuiltin();
    }

    public function getType(): DataType
    {
        return new DataType($this->property->getType());
    }

    public function __call($name, $arguments)
    {
        return $this->property->$name(...$arguments);
    }
}
