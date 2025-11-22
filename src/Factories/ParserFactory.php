<?php

namespace Mementohub\Data\Factories;

use Mementohub\Data\Attributes\MapInputName;
use Mementohub\Data\Attributes\StripValues;
use Mementohub\Data\Contracts\Normalizer;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\DataParser;
use Mementohub\Data\Parsers\EnumParser;
use Mementohub\Data\Parsers\InputMappingParser;
use Mementohub\Data\Parsers\MultiParser;
use Mementohub\Data\Parsers\StrippingValuesParser;

class ParserFactory
{
    protected static array $resolved = [];

    protected DataClass $class;

    public static function for(string $class): ?Parser
    {
        return static::$resolved[$class] ??= new self($class)->resolve();
    }

    public function __construct(string $class)
    {
        $this->class = new DataClass($class);
    }

    protected function resolve(): ?Parser
    {
        $parsers = array_filter([
            ...$this->resolveNormalizers(),
            ...$this->resolveInputMapper(),
            ...$this->resolveStrippingValues(),
            ...$this->resolveParsers(),
        ]);

        return match (count($parsers)) {
            0 => null,
            1 => $parsers[0],
            default => new MultiParser($this->class, $parsers),
        };
    }

    /** @return Parser[] */
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

    /** @return Parser[] */
    protected function resolveInputMapper(): array
    {
        if (! $this->class->hasAttribute(MapInputName::class)) {
            return [];
        }

        return [new InputMappingParser($this->class)];
    }

    /** @return Parser[] */
    protected function resolveStrippingValues(): array
    {
        if (! $this->class->hasAttribute(StripValues::class)) {
            return [];
        }

        return [new StrippingValuesParser($this->class)];
    }

    /** @return Parser[] */
    protected function resolveParsers(): array
    {
        if ($this->class->isEnum()) {
            return [new EnumParser($this->class)];
        }

        if ($this->class->isSubclassOf(Data::class)) {
            return [new DataParser($this->class)];
        }

        return [];
    }
}
