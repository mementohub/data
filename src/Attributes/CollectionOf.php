<?php

namespace Mementohub\Data\Attributes;

use Attribute;
use Generator;
use Mementohub\Data\Casters\CollectionCaster;
use Mementohub\Data\Casters\GeneratorCaster;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Traversable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CollectionOf
{
    protected readonly array $arguments;

    public function __construct(
        public readonly string $class,
        mixed ...$arguments
    ) {
        $this->arguments = $arguments;
    }

    public function make(DataProperty $property): Caster
    {
        if ($property->getType()->firstOf(Traversable::class) === Generator::class) {
            return new GeneratorCaster($property, $this->class, ...$this->arguments);
        }

        return new CollectionCaster($property, $this->class, ...$this->arguments);
    }
}
