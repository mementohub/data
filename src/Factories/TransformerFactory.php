<?php

namespace Mementohub\Data\Factories;

use Mementohub\Data\Attributes\TransformUsing;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Data;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Transformers\CollectionTransformer;
use Mementohub\Data\Transformers\DataTransformer;
use Mementohub\Data\Transformers\DateTimeTransformer;
use Mementohub\Data\Transformers\EnumTransformer;
use Mementohub\Data\Transformers\RecursiveTransformer;

class TransformerFactory
{
    public static array $resolved = [];

    public static array $resolving = [];

    protected static array $exceptions = [];

    protected DataClass $class;

    public static function for(string $class): ?Transformer
    {
        if (array_key_exists($class, static::$resolving)) {
            return new RecursiveTransformer($class);
        }
        static::$resolving[$class] = true;

        $resolved = static::$resolved[$class] ??= new self($class)->resolve();

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

        return null;
    }

    public static function setExceptions(Data $source, array $except): void
    {
        static::$exceptions[spl_object_id($source)] = $except;
    }

    public static function getExceptions(Data $source): array
    {
        return static::$exceptions[spl_object_id($source)] ?? [];
    }

    public function __construct(string $class)
    {
        $this->class = new DataClass($class);
    }

    protected function resolve(): ?Transformer
    {
        if ($this->class->isEnum()) {
            return new EnumTransformer;
        }

        if ($this->class->isSubclassOf(Data::class)) {
            return new DataTransformer($this->class);
        }

        return null;
    }
}
