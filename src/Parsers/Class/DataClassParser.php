<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;
use Mementohub\Data\Parsers\Factories\PropertyParserFactory;

class DataClassParser implements ClassParser
{
    protected array $property_processors = [];

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->setPropertyProcesors();
    }

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        foreach (array_intersect_key($this->property_processors, $data) as $property => $processor) {
            $data[$property] = $processor->parse($data[$property], $data);
        }

        return $this->class->buildFromWithDefaults($data);
    }

    protected function setPropertyProcesors()
    {
        foreach ($this->class->getProperties() as $property) {
            if (! $property->needsParsing()) {
                continue;
            }

            $this->property_processors[$property->getName()] = PropertyParserFactory::for($property);
        }
    }
}
