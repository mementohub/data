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
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Parsers\Property\DataPropertyParser;

class Casters
{
    public function __construct(
        protected readonly DataProperty $property
    ) {}

    /** @return Caster[] */
    public static function for(DataProperty $property): array
    {
        return new self($property)->resolve();
    }

    /** @return Caster[] */
    protected function resolve(): array
    {
        return array_filter([
            ...$this->getSpecificCasters(),
            ...$this->getDataCaster(),
        ]);
    }

    protected function getDataCaster(): array
    {
        if ($this->property->getType()->firstOf(Data::class)) {
            return [new DataPropertyParser($this->property)];
        }

        return [];
    }

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
