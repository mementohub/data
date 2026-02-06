<?php

namespace Mementohub\Data\Entities;

use Mementohub\Data\Data;
use Mementohub\Data\Values\Optional;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

/**
 * @mixin ReflectionNamedType
 * @mixin ReflectionUnionType
 */
class DataType
{
    public function __construct(
        protected readonly ?ReflectionType $type
    ) {}

    public function firstOf(string $abstract): ?string
    {
        foreach ($this->getTypes() as $type) {
            if ($this->is($type, $abstract)) {
                return $type->getName();
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

        if ($this->type instanceof ReflectionNamedType) {
            return [$this->type];
        }

        return [];
    }

    public function getMainType(): ?string
    {
        [$candidates, $optionals] = $this->partition($this->getTypes(), fn (ReflectionNamedType $type) => $type->getName() !== Optional::class);

        [$data, $others] = $this->partition($candidates, fn (ReflectionNamedType $type) => $this->is($type, Data::class));

        if (array_key_exists(0, $data)) {
            return $data[0]->getName();
        }

        [$custom, $others] = $this->partition($others, fn (ReflectionNamedType $type) => ! $type->isBuiltin());

        if (array_key_exists(0, $custom)) {
            return $custom[0]->getName();
        }

        return null;
    }

    public function allows(string $abstract): bool
    {
        foreach ($this->getTypes() as $type) {
            if ($this->is($type, $abstract)) {
                return true;
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

    public function is(ReflectionNamedType $type, string $abstract): bool
    {
        $name = $type->getName();

        if ($name === $abstract) {
            return true;
        }
        if (is_a($name, $abstract, true)) {
            return true;
        }

        return false;
    }

    protected function partition(array $array, callable $callback): array
    {
        $matched = [];
        $unmatched = [];

        foreach ($array as $item) {
            if ($callback($item)) {
                $matched[] = $item;
            } else {
                $unmatched[] = $item;
            }
        }

        return [$matched, $unmatched];
    }

    public function __call($name, $arguments)
    {
        return $this->type->$name(...$arguments);
    }
}
