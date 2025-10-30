<?php

namespace Mementohub\Data\Parsers\Factories;

use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Contracts\PropertyParser;
use Mementohub\Data\Parsers\Property\CastablePropertyParser;
use Mementohub\Data\Parsers\Property\DataPropertyParser;
use Mementohub\Data\Parsers\Property\PlainPropertyParser;

class PropertyParserFactory
{
    public function __construct(
        protected readonly DataProperty $property
    ) {}

    public static function for(DataProperty $property): PropertyParser
    {
        return new self($property)->resolve();
    }

    protected function resolve(): PropertyParser
    {
        $casters = $this->getUserDefinedCasters();

        if (count($casters) > 0) {
            return (new CastablePropertyParser($this->property, $casters))
                ->then($this->getPropertyParser());
        }

        return $this->getPropertyParser();
    }

    protected function getPropertyParser(): PropertyParser
    {
        if ($this->property->getType()->isBuiltin()) {
            return new PlainPropertyParser($this->property);
        }

        return new DataPropertyParser($this->property);
    }

    protected function getUserDefinedCasters(): array
    {
        $casters = [];

        /** @var ReflectionAttribute $caster */
        foreach ($this->property->getCastableAttributes() as $caster) {
            $casters[] = $caster->newInstance()->make($this->property);
        }

        return $casters;
    }
}
