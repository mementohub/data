<?php

namespace Mementohub\Data\Entities;

use BackedEnum;
use DateTimeInterface;
use Mementohub\Data\Values\Optional;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionProperty;
use Traversable;

/**
 * @mixin ReflectionProperty
 */
class DataProperty
{
    public function __construct(
        protected readonly ReflectionProperty $property
    ) {}

    public function isEnum(): bool
    {
        return $this->getType()->firstOf(BackedEnum::class) !== null;
    }

    public function isDateTime(): bool
    {
        return $this->getType()->firstOf(DateTimeInterface::class) !== null;
    }

    public function isTraversable(): bool
    {
        return $this->getType()->firstOf(Traversable::class) !== null
            || $this->getType()->firstOf('array') !== null;
    }

    public function allowsOptional(): bool
    {
        return $this->getType()->allows(Optional::class);
    }

    public function allowsNull(): bool
    {
        return $this->getType()->allowsNull();
    }

    public function inferArrayTypeFromDocBlock(): ?string
    {
        if (! $this->property->getDocComment()) {
            return null;
        }

        $factory = DocBlockFactory::createInstance();
        $docblock = $factory->create($this->property->getDocComment());

        /** @var \phpDocumentor\Reflection\DocBlock\Tag $tag */
        foreach ($docblock->getTagsByName('var') as $tag) {
            if (! ($type = $tag->getType()) instanceof Array_) {
                continue;
            }

            if (! ($type = $type->getValueType()) instanceof Object_) {
                continue;
            }

            $type = (string) $type;

            if (class_exists($type)) {
                return $type;
            }

            $type = ltrim($type, '\\');

            $fqsen = $this->property->getDeclaringClass()->getNamespaceName().'\\'.$type;

            if (class_exists($fqsen)) {
                return $fqsen;
            }

            $file = file_get_contents($this->property->getDeclaringClass()->getFileName());
            if (preg_match('/use\s+(\S+\\\\'.$type.');/', $file, $matches)) {
                return $matches[1];
            }

            if (preg_match('/use\s+(\S+)\s+as\s+'.$type.';/', $file, $matches)) {
                return $matches[1];
            }

            return null;
        }

        return null;
    }

    public function getFirstAttributeInstance(string $name): mixed
    {
        $attributes = $this->property->getAttributes($name);

        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    public function getType(): DataType
    {
        return new DataType($this->property->getType());
    }

    public function __call($name, $arguments)
    {
        return $this->property->$name(...$arguments);
    }
}
