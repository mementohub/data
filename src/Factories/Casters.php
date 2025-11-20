<?php

namespace Mementohub\Data\Factories;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Mementohub\Data\Attributes\CastUsing;
use Mementohub\Data\Attributes\CollectionOf;
use Mementohub\Data\Casters\CollectionCaster;
use Mementohub\Data\Casters\DateTimeCaster;
use Mementohub\Data\Casters\EnumCaster;
use Mementohub\Data\Casters\MultiCaster;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Property\DataPropertyParser;

class Casters
{
    public function __construct(
        protected readonly DataProperty $property
    ) {}

    public static function for(DataProperty $property): Caster
    {
        return new self($property)->resolve();
    }

    protected function resolve(): Caster
    {
        $casters = array_filter([
            ...$this->getSpecificCasters(),
            ...$this->getDataCaster(),
        ]);

        if (count($casters) !== 1) {
            return new MultiCaster($this->property, $casters);
        }

        return $casters[0];
    }

    protected function getDataCaster(): array
    {
        if ($this->property->getType()->firstOf(Data::class)) {
            return [new DataPropertyParser($this->property)];
        }

        return [];
    }

    /** @return Caster[] */
    protected function getSpecificCasters(): array
    {
        $userDefined = $this->getUserDefinedCasters();
        if (count($userDefined) > 0) {
            return $userDefined;
        }

        return $this->getInferredCasters();
    }

    /** @return Caster[] */
    protected function getUserDefinedCasters(): array
    {
        $resolved = [];

        foreach ($this->property->getAttributes() as $attribute) {
            if (! in_array($attribute->getName(), [
                CastUsing::class,
                CollectionOf::class,
            ])) {
                continue;
            }

            $resolved[] = $attribute->newInstance()->make($this->property);
        }

        return $resolved;
    }

    /** @return Caster[] */
    protected function getInferredCasters(): array
    {
        $type = $this->property->getType();

        if ($enum = $type->firstOf(BackedEnum::class)) {
            return [new EnumCaster($this->property, $enum)];
        }

        if ($type->firstOf(Collection::class) || $type->firstOf('array')) {
            $class = $this->property->inferArrayTypeFromDocBlock();

            return [new CollectionCaster($this->property, $class)];
        }

        if ($type->firstOf(DateTimeInterface::class)) {
            return [new DateTimeCaster($this->property)];
        }

        return [];
    }
}
