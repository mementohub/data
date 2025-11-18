<?php

namespace Mementohub\Data\Parsers\Factories;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Mementohub\Data\Casters\CollectionCaster;
use Mementohub\Data\Casters\DateTimeCaster;
use Mementohub\Data\Casters\EnumCaster;
use Mementohub\Data\Data;
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

    public static function for(DataProperty $property): ?PropertyParser
    {
        return new self($property)->resolve();
    }

    protected function resolve(): ?PropertyParser
    {
        $caster = $this->getCaster();
        $parser = $this->getPropertyParser();

        if ($caster == null) {
            return $parser;
        }

        if ($parser == null) {
            return $caster;
        }

        return $caster;

        return $caster->then($parser);
    }

    protected function getCaster(): ?PropertyParser
    {
        $casters = $this->getUserDefinedCasters();

        if (count($casters) > 0) {
            return $casters[0];

            return new CastablePropertyParser($this->property, $casters);
        }

        if ($caster = $this->getInferredCasters()) {
            return $caster;

            return new CastablePropertyParser($this->property, [$caster]);
        }

        return null;
    }

    protected function getPropertyParser(): ?PropertyParser
    {
        if ($this->property->getType()->isBuiltin()) {
            return new PlainPropertyParser($this->property);

            return null;
        }

        if ($this->property->getType()->firstOf(Data::class)) {
            return new DataPropertyParser($this->property);
        }

        return null;

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

    protected function getInferredCasters(): ?PropertyParser
    {
        $type = $this->property->getType();

        if ($enum = $type->firstOf(BackedEnum::class)) {
            return new EnumCaster($this->property, $enum);
        }

        if ($type->firstOf(Collection::class) || $type->firstOf('array')) {
            $class = $this->property->inferArrayTypeFromDocBlock();

            return new CollectionCaster($this->property, $class);
        }

        if ($type->firstOf(DateTimeInterface::class)) {
            return new DateTimeCaster($this->property);
        }

        return null;
    }
}
