<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Exceptions\CastingException;

class EnumCaster implements Caster
{
    protected array $cached = [];

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly string $class,
    ) {}

    public function handle(mixed $value, array $context): mixed
    {
        if (is_string($value) || is_int($value)) {
            try {
                return $this->class::from($value);
            } catch (\Throwable $t) {
                throw new CastingException('Unable to create '.$this->class.' from '.$value, $this->property, $value, $t);
            }
        }

        return $value;
    }
}
