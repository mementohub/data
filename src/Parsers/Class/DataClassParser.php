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

        $processed = [];
        foreach ($this->property_processors as $property => $processor) {
            if (isset($data[$property])) {
                if ($processor === null) {
                    $processed[] = $data[$property];
                } else {
                    $value = $data[$property];
                    foreach ([$processor] as $p) {
                        $value = $p->parse($value, $data);
                    }
                    $processed[] = $value;
                }
            } else {
                $processed[] = $this->class->defaults[$property];
            }

        }

        return new $this->class->name(...$processed);
    }

    protected function setPropertyProcesors()
    {
        foreach ($this->class->getProperties() as $property) {
            if (! $property->needsParsing()) {
                $this->property_processors[$property->getName()] = null;

                continue;
            }

            $this->property_processors[$property->getName()] = PropertyParserFactory::for($property);
        }
    }
}
