<?php

namespace Mementohub\Data\Entities;

use Mementohub\Data\Attributes\CastUsing;
use Mementohub\Data\Attributes\CollectionOf;
use Mementohub\Data\Values\Optional;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionAttribute;
use ReflectionProperty;

/**
 * @mixin ReflectionProperty
 */
class DataProperty
{
    public function __construct(
        protected readonly ReflectionProperty $property
    ) {}

    public function allowsOptional(): bool
    {
        return $this->getType()->allows(Optional::class);
    }

    public function allowsNull(): bool
    {
        return $this->getType()->allowsNull();
    }

    public function needsParsing(): bool
    {
        if ($this->isCastable()) {
            return true;
        }

        return ! $this->getType()->isBuiltin();
    }

    public function getCastableAttributes(): array
    {
        return array_filter(
            $this->property->getAttributes(),
            fn (ReflectionAttribute $attribute) => in_array($attribute->getName(), [
                CastUsing::class,
                CollectionOf::class,
            ])
        );
    }

    public function inferArrayTypeFromDocBlock(): ?string
    {
        if (! $this->property->getDocComment()) {
            return null;
        }

        $factory = DocBlockFactory::createInstance();
        $docblock = $factory->create($this->property->getDocComment());

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

    protected function isCastable(): bool
    {
        if ($this->getType()->firstOf('array')) {
            return true;
        }

        return count($this->getCastableAttributes()) > 0;
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
