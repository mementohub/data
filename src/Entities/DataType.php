<?php

namespace Mementohub\Data\Entities;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;

/**
 * @mixin ReflectionNamedType
 * @mixin ReflectionUnionType
 */
class DataType
{
    public function __construct(
        protected readonly ReflectionType $type
    ) {}

    public function firstOf(string $abstract): ?string
    {
        foreach ($this->getTypes() as $type) {
            $name = $type->getName();
            if ($name === $abstract) {
                return $name;
            }
            if (is_a($name, $abstract, true)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * @return array<int, ReflectionNamedType>
     */
    public function getTypes(): array
    {
        if ($this->type instanceof ReflectionUnionType) {
            /** @var array<int, ReflectionNamedType> */
            return $this->type->getTypes();
        }

        /** @var ReflectionNamedType $type */
        $type = $this->type;

        return [$type];
    }

    public function getMainType(): string
    {
        if ($this->type instanceof ReflectionNamedType) {
            return $this->type->getName();
        }

        if ($this->type instanceof ReflectionUnionType) {
            foreach ($this->type->getTypes() as $type) {
                return $type;
            }
        }

        throw new RuntimeException('Unable to find main type');
    }

    public function allows(string $type): bool
    {
        if ($this->type instanceof ReflectionNamedType) {
            return $this->type->getName() === $type;
        }

        if ($this->type instanceof ReflectionUnionType) {
            foreach ($this->type->getTypes() as $namedType) {
                if ($namedType->getName() === $type) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isBuiltin(): bool
    {
        foreach ($this->getTypes() as $type) {
            if (! $type->isBuiltin()) {
                return false;
            }
        }

        return true;
    }

    public function __call($name, $arguments)
    {
        return $this->type->$name(...$arguments);
    }
}
