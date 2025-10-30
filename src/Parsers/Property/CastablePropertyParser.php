<?php

namespace Mementohub\Data\Parsers\Property;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\PropertyParser;

class CastablePropertyParser implements PropertyParser
{
    protected PropertyParser $next;

    public function __construct(
        public readonly DataProperty $property,
        /** @var Caster[] */
        protected readonly array $casters
    ) {}

    public function parse(mixed $value, array $context): mixed
    {
        foreach ($this->casters as $caster) {
            $value = $caster->cast($value, $context);
        }

        return $this->next->parse($value, $context);
    }

    public function then(PropertyParser $next): PropertyParser
    {
        $this->next = $next;

        return $this;
    }
}
