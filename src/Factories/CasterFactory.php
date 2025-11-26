<?php

namespace Mementohub\Data\Factories;

use BackedEnum;
use Generator;
use Mementohub\Data\Attributes\CastUsing;
use Mementohub\Data\Attributes\CollectionOf;
use Mementohub\Data\Casters\CollectionCaster;
use Mementohub\Data\Casters\DataCaster;
use Mementohub\Data\Casters\DateTimeCaster;
use Mementohub\Data\Casters\EnumCaster;
use Mementohub\Data\Casters\GeneratorCaster;
use Mementohub\Data\Casters\MultiCaster;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Traversable;

class CasterFactory
{
    public function __construct(
        protected readonly DataProperty $property
    ) {}

    public static function for(DataProperty $property): ?Caster
    {
        return new self($property)->resolve();
    }

    protected function resolve(): ?Caster
    {
        $casters = array_filter([
            ...$this->getSpecificCasters(),
            ...$this->getDataCaster(),
        ]);

        return match (count($casters)) {
            0 => null,
            1 => $casters[0],
            default => new MultiCaster($this->property, $casters),
        };
    }

    protected function getDataCaster(): array
    {
        if ($this->property->isData()) {
            return [new DataCaster($this->property)];
        }

        if ($this->property->isEnum()
            || $this->property->isTraversable()
            || $this->property->isDateTime()
            || $this->property->getType()->isBuiltin()
        ) {
            return [];
        }

        // regular objects
        return [new DataCaster($this->property)];
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

        foreach ([CastUsing::class, CollectionOf::class] as $attribute) {
            if ($attribute = $this->property->getFirstAttributeInstance($attribute)) {
                $resolved[] = $attribute->make($this->property);
            }
        }

        return $resolved;
    }

    /** @return Caster[] */
    protected function getInferredCasters(): array
    {
        $type = $this->property->getType();

        if ($this->property->isEnum()) {
            return [new EnumCaster($this->property, $type->firstOf(BackedEnum::class))];
        }

        if ($this->property->isTraversable()) {
            $class = $this->property->inferArrayTypeFromDocBlock();

            if ($type->firstOf(Traversable::class) === Generator::class) {
                return [new GeneratorCaster($this->property, $class)];
            }

            return [new CollectionCaster($this->property, $class)];
        }

        if ($this->property->isDateTime()) {
            return [new DateTimeCaster($this->property)];
        }

        return [];
    }
}
