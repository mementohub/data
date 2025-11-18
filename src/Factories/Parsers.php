<?php

namespace Mementohub\Data\Factories;

use Mementohub\Data\Attributes\MapInputName;
use Mementohub\Data\Attributes\StripValues;
use Mementohub\Data\Contracts\Normalizer;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Class\DataClassParser;
use Mementohub\Data\Parsers\Class\EnumClassParser;
use Mementohub\Data\Parsers\Class\InputMappingClassParser;
use Mementohub\Data\Parsers\Class\StrippingValuesClassParser;

class Parsers
{
    protected static array $resolved = [];

    protected DataClass $class;

    /** @return Parser[] */
    public static function for(string $class): array
    {
        return static::$resolved[$class] ??= new self($class)->resolve();
    }

    public function __construct(string $class)
    {
        $this->class = new DataClass($class);
    }

    /** @return Parser[] */
    protected function resolve(): array
    {
        return array_filter([
            ...$this->resolveNormalizers(),
            ...$this->resolveInputMapper(),
            ...$this->resolveStrippingValues(),
            ...$this->resolveParsers(),
        ]);
    }

    protected function resolveNormalizers(): array
    {
        if (! is_a($this->class->name, Data::class, true)) {
            return [];
        }

        $resolved = [];
        foreach ($this->class->name::normalizers() as $normalizer) {
            if (is_string($normalizer) && class_exists($normalizer)) {
                $normalizer = new $normalizer;
            }

            if (is_a($normalizer, Normalizer::class, true)) {
                $resolved[] = $normalizer;
            }
        }

        return $resolved;
    }

    protected function resolveInputMapper(): array
    {
        if (! $this->class->hasAttribute(MapInputName::class)) {
            return [];
        }

        return [new InputMappingClassParser($this->class)];
    }

    protected function resolveStrippingValues(): array
    {
        if (! $this->class->hasAttribute(StripValues::class)) {
            return [];
        }

        return [new StrippingValuesClassParser($this->class)];
    }

    protected function resolveParsers(): array
    {
        if ($this->class->isEnum()) {
            return [new EnumClassParser($this->class)];
        }

        if ($this->class->isDataClass()) {
            return [new DataClassParser($this->class)];
        }

        return [];
    }
}
