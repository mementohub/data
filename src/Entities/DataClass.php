<?php

namespace Mementohub\Data\Entities;

use ReflectionClass;

class DataClass
{
    protected ReflectionClass $class;

    /** @var DataProperty[] */
    protected array $properties = [];

    public function __construct(
        string $class,
    ) {
        $this->class = new ReflectionClass($class);
        $this->setProperties();
    }

    public function getName(): string
    {
        return $this->class->getName();
    }

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

    public function getNullableProperties(): array
    {
        return array_filter($this->properties, fn (DataProperty $property) => $property->isNullable());
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

            if ($property->isNullable()) {
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
        foreach ($this->class->getConstructor()->getParameters() as $property) {
            $this->properties[$property->getName()] = new DataProperty($property);
        }
    }
}
