<?php

namespace Mementohub\Data\Parsers\Class;

use Exception;
use Mementohub\Data\Attributes\StripValues;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;

class StrippingValuesClassParser implements ClassParser
{
    protected ClassParser $next;

    protected readonly array $strippers;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->strippers = $this->resolveStrippers();
    }

    public function parse(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        try {
            return $this->next->parse(
                $this->stripInput($data)
            );
        } catch (Exception $e) {
            throw new Exception('Unable to strip input data', $e->getCode(), $e);
        }
    }

    public function then(ClassParser $next): ClassParser
    {
        $this->next = $next;

        return $this;
    }

    protected function stripInput(array $data): array
    {
        foreach (array_intersect_key($this->strippers, $data) as $key => $targets) {
            if (in_array($data[$key], $targets)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    protected function resolveStrippers(): array
    {

        foreach ($this->class->getProperties() as $property) {
            $attributes = $property->getAttributes(StripValues::class);
            if (count($attributes) === 0) {
                continue;
            }

            $mappers[$property->getName()] = $attributes[0]->getArguments();
        }

        return $mappers;
    }
}
