<?php

namespace Mementohub\Data\Parsers;

use Exception;
use Mementohub\Data\Attributes\StripValues;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;

class StrippingValuesParser implements Parser
{
    protected readonly array $strippers;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->strippers = $this->resolveStrippers();
    }

    public function handle(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        try {
            return $this->stripInput($data);
        } catch (Exception $e) {
            throw new Exception('Unable to strip input data', $e->getCode(), $e);
        }
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
        $strippers = [];
        foreach ($this->class->getProperties() as $property) {
            if ($attribute = $property->getFirstAttributeInstance(StripValues::class)) {
                $strippers[$property->getName()] = $attribute->arguments;
            }
        }

        return $strippers;
    }
}
