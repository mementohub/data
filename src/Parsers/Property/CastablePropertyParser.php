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
            return $caster->cast($value, $context);
        }

        if (isset($this->next)) {
            return $this->next->parse($value, $context);
        }

        return $value;
    }

    public function then(PropertyParser $next): PropertyParser
    {
        $this->next = $next;

        return $this;
    }
}
