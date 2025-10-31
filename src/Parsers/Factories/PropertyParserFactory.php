<?php

namespace Mementohub\Data\Parsers\Factories;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Mementohub\Data\Casters\CollectionCaster;
use Mementohub\Data\Casters\DateTimeCaster;
use Mementohub\Data\Casters\EnumCaster;
use Mementohub\Data\Contracts\Caster;
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

        if ($caster = $this->getInferredCasters()) {
            return (new CastablePropertyParser($this->property, [$caster]))
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

    protected function getInferredCasters(): ?Caster
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
