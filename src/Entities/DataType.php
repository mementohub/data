<?php

namespace Mementohub\Data\Entities;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;

class DataType
{
    public function __construct(
        protected readonly ReflectionType $type
    ) {}

    public function isBuiltin(): bool
    {
        return ($this->type instanceof ReflectionNamedType)
            && $this->type->isBuiltin();
    }

    public function getMainType(): string
    {
        if ($this->type instanceof ReflectionNamedType) {
            return $this->type->getName();
        }

        if ($this->type instanceof ReflectionUnionType) {
            foreach ($this->type->getTypes() as $type) {
                // if ($type->getName() !== Optional::class) {
                return $type;
                // }
            }
        }

        throw new RuntimeException('Unable to find main type');
    }
}
