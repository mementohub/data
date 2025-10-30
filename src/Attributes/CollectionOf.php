<?php

namespace Mementohub\Data\Attributes;

use Attribute;
use Mementohub\Data\Casters\CollectionCaster;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CollectionOf
{
    protected readonly array $arguments;

    public function __construct(
        mixed ...$arguments
    ) {
        $this->arguments = $arguments;
    }

    public function make(DataProperty $property): Caster
    {
        return new CollectionCaster($property, ...$this->arguments);
    }
}
