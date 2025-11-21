<?php

namespace Mementohub\Data\Entities;

use Mementohub\Data\Values\Optional;
use ReflectionClass;

/**
 * @mixin ReflectionClass;
 */
class DataClass
{
    public readonly string $name;

    public readonly array $defaults;

    protected ReflectionClass $class;

    /** @var DataProperty[] */
    protected array $properties = [];

    public function __construct(
        string $class_name,
    ) {
        $this->class = new ReflectionClass($class_name);
        $this->name = $this->class->getName();
        $this->setProperties();
        $this->defaults = $this->computeDefaultValues();
    }

    public function hasAttribute(string $name): bool
    {
        foreach ($this->class->getProperties() as $property) {
            if ($property->getAttributes($name)) {
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
        $defined = [];
        foreach ($this->class->getConstructor()?->getParameters() ?? [] as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $defined[] = $parameter->getName();
            }
        }

        $defaults = [];
        foreach ($this->properties as $name => $property) {
            if ($property->hasDefaultValue() || in_array($property->getName(), $defined)) {
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
