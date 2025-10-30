<?php

namespace Mementohub\Data\Attributes;

use Attribute;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class CastUsing
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
        return new $this->class($property, ...$this->arguments);
    }
}
