<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

class MultiCaster implements Caster
{
    public function __construct(
        protected readonly DataProperty $property,
        /** @var Caster[] */
        protected readonly array $casters,
    ) {}

    public function handle(mixed $value, array $context): mixed
    {
        foreach ($this->casters as $caster) {
            $value = $caster->handle($value, $context);
        }

        return $value;
    }
}
