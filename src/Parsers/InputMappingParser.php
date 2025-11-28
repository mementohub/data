<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Attributes\MapInputName;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Exceptions\ParsingException;

class InputMappingParser implements Parser
{
    protected Parser $next;

    protected readonly array $mappers;

    protected readonly bool $has_nested_mappers;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->mappers = $this->resolveInputMappers();
        $this->has_nested_mappers = $this->detectNestedMappers();
    }

    public function handle(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        try {
            return $this->mapInput($value);
        } catch (\Throwable $t) {
            throw new ParsingException('Failed to map input for these mappers:'.print_r($this->mappers, true), $this->class, $value, $t);
        }
    }

    protected function mapInput(array $data): array
    {
        if ($this->has_nested_mappers) {
            return $this->mapNestedInput($data);
        }

        return $this->mapSimpleInput($data);
    }

    protected function mapSimpleInput(array $data): array
    {
        foreach ($this->mappers as $from => $to) {
            $data[$to] = $data[$from];
        }

        return $data;
    }

    protected function mapNestedInput(array $data): array
    {
        foreach ($this->mappers as $from => $to) {
            $data[$to] = $this->getNestedValue($data, explode('.', $from));
        }

        return $data;
    }

    protected function getNestedValue(array $data, array $path): mixed
    {
        $key = array_shift($path);

        if (! array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];

        if (count($path) === 0) {
            return $value;
        }

        if (is_array($value)) {
            return $this->getNestedValue($value, $path);
        }

        return null;
    }

    protected function resolveInputMappers(): array
    {
        $mappers = [];

        foreach ($this->class->getProperties() as $property) {
            if ($attribute = $property->getFirstAttributeInstance(MapInputName::class)) {
                $mappers[$attribute->input] = $property->getName();
            }
        }

        return $mappers;
    }

    protected function detectNestedMappers(): bool
    {
        foreach ($this->mappers as $from => $to) {
            if (str_contains($from, '.')) {
                return true;
            }
        }

        return false;
    }
}
