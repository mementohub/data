<?php

namespace Mementohub\Data\Factories;

use Mementohub\Data\Attributes\MapOutputName;
use Mementohub\Data\Attributes\TransformUsing;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Transformers\CollectionTransformer;
use Mementohub\Data\Transformers\DataTransformer;
use Mementohub\Data\Transformers\DateTimeTransformer;
use Mementohub\Data\Transformers\EnumTransformer;
use Mementohub\Data\Transformers\MultiTransformer;
use Mementohub\Data\Transformers\OutputMappingTransformer;
use Mementohub\Data\Transformers\RecursiveTransformer;

class TransformerFactory
{
    public static array $resolved = [];

    public static array $resolving = [];

    protected static array $exceptions = [];

    protected DataClass $class;

    protected readonly array $except;

    public static function for(?string $class, array $except = []): ?Transformer
    {
        if (is_null($class) || ! class_exists($class)) {
            return null;
        }

        if (array_key_exists($class, static::$resolving)) {
            return new RecursiveTransformer($class);
        }
        static::$resolving[$class] = true;

        if (count($except) > 0) {
            $resolved = new self($class, $except)->resolve();
        } else {
            $resolved = static::$resolved[$class] ??= new self($class)->resolve();
        }

        unset(static::$resolving[$class]);

        return $resolved;
    }

    public static function forProperty(DataProperty $property): ?Transformer
    {
        if ($attribute = $property->getFirstAttributeInstance(TransformUsing::class)) {
            return $attribute->make($property);
        }

        if ($property->isEnum()) {
            return new EnumTransformer;
        }

        if ($property->isDateTime()) {
            return new DateTimeTransformer($property);
        }

        if ($property->isTraversable()) {
            return new CollectionTransformer($property);
        }

        if ($property->isData()) {
            return self::for($property->getType()->firstOf(Data::class));
        }

        if ($property->getType()->isBuiltin()) {
            return null;
        }

        return self::for($property->getType()->getMainType());
    }

    public static function setExceptions(Data $source, array $except): void
    {
        static::$exceptions[spl_object_id($source)] = $except;
    }

    public static function getExceptions(Data $source): array
    {
        return static::$exceptions[spl_object_id($source)] ?? [];
    }

    public function __construct(string $class, array $except = [])
    {
        $this->class = new DataClass($class);
        $this->except = $except;
    }

    protected function resolve(): ?Transformer
    {
        if ($this->class->isEnum()) {
            return new EnumTransformer;
        }

        if ($this->class->isInternal()) {
            return null;
        }

        $transformers = [
            ...$this->resolveTransformers(),
            ...$this->resolveOutputMapper(),
        ];

        return match (count($transformers)) {
            0 => null,
            1 => $transformers[0],
            default => new MultiTransformer($transformers),
        };
    }

    /** @return Transformer[] */
    protected function resolveTransformers(): array
    {
        return [new DataTransformer($this->class, $this->except)];
    }

    /** @return Transformer[] */
    protected function resolveOutputMapper(): array
    {
        if (! $this->class->hasAttribute(MapOutputName::class)) {
            return [];
        }

        return [new OutputMappingTransformer($this->class)];
    }
}
