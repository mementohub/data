<?php

namespace Mementohub\Data\Entities;

use Mementohub\Data\Attributes\CastUsing;
use Mementohub\Data\Attributes\CollectionOf;
use Mementohub\Data\Values\Optional;
use ReflectionAttribute;
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
        if ($this->isCastable()) {
            return true;
        }

        return ! $this->getType()->isBuiltin();
    }

    public function getCastableAttributes(): array
    {
        return array_filter(
            $this->property->getAttributes(),
            fn (ReflectionAttribute $attribute) => in_array($attribute->getName(), [
                CastUsing::class,
                CollectionOf::class,
            ])
        );
    }

    protected function isCastable(): bool
    {
        return count($this->getCastableAttributes()) > 0;
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
