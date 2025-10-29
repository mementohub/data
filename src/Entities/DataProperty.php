<?php

namespace Mementohub\Data\Entities;

use ReflectionParameter;

class DataProperty
{
    public function __construct(
        protected readonly ReflectionParameter $property
    ) {}

    public function isNullable(): bool
    {
        return $this->property->allowsNull();
    }

    public function hasDefaultValue(): bool
    {
        return $this->property->isDefaultValueAvailable();
    }

    public function getDefaultValue(): mixed
    {
        return $this->property->getDefaultValue();
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getType(): DataType
    {
        return new DataType($this->property->getType());
    }
}
