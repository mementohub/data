<?php

namespace Mementohub\Data\Entities;

use Mementohub\Data\Attributes\MapInputName;
use Mementohub\Data\Values\Optional;
use ReflectionClass;

/**
 * @mixin ReflectionClass;
 */
class DataClass
{
    public readonly string $name;

    public readonly array $defaults;

    public readonly array $known_properties;

    protected ReflectionClass $class;

    /** @var DataProperty[] */
    protected array $properties = [];

    public function __construct(
        string $class_name,
    ) {
        $this->class = new ReflectionClass($class_name);
        $this->name = $this->class->getName();
        $this->setProperties();
        $this->known_properties = $this->getPropertyKeys();
        $this->defaults = $this->computeDefaultValues();
    }

    public function buildFrom(array $data): mixed
    {
        return new $this->name(...$this->acceptableParameters($data));
    }

    public function buildFromWithDefaults(array $data): mixed
    {
        return new $this->name(...$this->acceptableParametersWithDefaults($data));
    }

    public function acceptableParameters(array $data): array
    {
        return array_intersect_key($data, $this->known_properties);
    }

    public function acceptableParametersWithDefaults(array $data): array
    {
        return array_merge($this->defaults, array_intersect_key($data, $this->known_properties));
    }

    public function needsNormalizing(): bool
    {
        return count($this->class->name::normalizers()) > 0;
    }

    public function needsInputMapping(): bool
    {
        foreach ($this->class->getProperties() as $property) {
            if ($property->getAttributes(MapInputName::class)) {
                return true;
            }
        }

        return false;
    }

    /** @return DataProperty[] */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getPropertyKeys(): array
    {
        $properties = [];
        foreach ($this->properties as $key => $property) {
            $properties[$key] = null;
        }

        return $properties;
    }

    protected function computeDefaultValues(): array
    {
        $defaults = [];
        foreach ($this->properties as $name => $property) {
            if ($property->hasDefaultValue()) {
                continue;
            }

            if ($property->allowsNull()) {
                $defaults[$name] = null;

                continue;
            }

            if ($property->allowsOptional()) {
                $defaults[$name] = Optional::create();
            }
        }

        return $defaults;
    }

    public function getNullableProperties(): array
    {
        return array_filter($this->properties, fn (DataProperty $property) => $property->allowsNull());
    }

    /**
     * Identify those properties that don't have a specific default value specified,
     * but they can accept a null value.
     */
    public function getNullDefaultableProperties(): array
    {
        $properties = [];

        foreach ($this->properties as $property) {
            if ($property->hasDefaultValue()) {
                continue;
            }

            if ($property->allowsNull()) {
                $properties[$property->getName()] = null;
            }
        }

        return $properties;
    }

    /**
     * A plain class is a class that has only built in types.
     * This means that it will not need any processing, as the type hints will be enough.
     */
    public function isPlainClass(): bool
    {
        foreach ($this->properties as $property) {
            if (! $property->getType()->isBuiltin()) {
                return false;
            }
        }

        return true;
    }

    protected function setProperties()
    {
        foreach ($this->class->getProperties() as $property) {
            if (! $property->isPromoted()) {
                continue;
            }
            $this->properties[$property->getName()] = new DataProperty($property);
        }
    }

    public function __call($name, $arguments)
    {
        return $this->class->$name(...$arguments);
    }
}
