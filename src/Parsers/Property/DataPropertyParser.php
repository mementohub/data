<?php

namespace Mementohub\Data\Parsers\Property;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Factories\Parsers;

class DataPropertyParser implements Caster
{
    /** @var Parser[] */
    protected readonly array $parsers;

    public function __construct(
        public readonly DataProperty $property
    ) {
        $this->parsers = Parsers::for($property->getType()->getMainType());
    }

    public function handle(mixed $value, array $context): mixed
    {
        foreach ($this->parsers as $parser) {
            $value = $parser->handle($value, $context);
        }

        return $value;
    }
}
