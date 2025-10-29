<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;
use Mementohub\Data\Parsers\Factories\PropertyParserFactory;

class DataClassParser implements ClassParser
{
    protected array $known_properties = [];

    protected string $className;

    protected array $property_processors = [];

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->className = $class->getName();
        $this->known_properties = $class->getPropertyKeys();
        $this->setPropertyProcesors();
    }

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        foreach ($this->property_processors as $property => $processor) {
            $data[$property] = $processor->parse($data[$property], $data);
        }

        return new $this->className(...array_intersect_key($data, $this->known_properties));
    }

    protected function setPropertyProcesors()
    {
        foreach ($this->class->getProperties() as $property) {
            $this->property_processors[$property->getName()] = PropertyParserFactory::for($property);
        }
    }
}
