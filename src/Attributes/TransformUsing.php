<?php

namespace Mementohub\Data\Attributes;

use Attribute;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TransformUsing
{
    protected readonly array $arguments;

    public function __construct(
        public readonly string $class,
        mixed ...$arguments
    ) {
        $this->arguments = $arguments;
    }

    public function make(DataProperty $property): Transformer
    {
        return new $this->class($property, ...$this->arguments);
    }
}
