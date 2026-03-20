<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Attributes\MapOutputName;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Exceptions\TransformingException;

class OutputMappingTransformer implements Transformer
{
    protected readonly array $mappers;

    public function __construct(
        protected readonly DataClass $class,
    ) {
        $this->mappers = $this->resolveOutputMappers();
    }

    public function handle(mixed $value): mixed
    {
        if (is_null($value) || ! is_array($value)) {
            return $value;
        }

        try {
            return $this->mapOutput($value);
        } catch (\Throwable $t) {
            throw new TransformingException('Failed to map output for these mappers:'.print_r($this->mappers, true), $value, $t);
        }
    }

    protected function mapOutput(array $data): array
    {
        foreach ($this->mappers as $from => $to) {
            if (! array_key_exists($from, $data) || $from === $to) {
                continue;
            }

            $data[$to] = $data[$from];
            unset($data[$from]);
        }

        return $data;
    }

    protected function resolveOutputMappers(): array
    {
        $mappers = [];

        foreach ($this->class->getProperties() as $property) {
            if ($attribute = $property->getFirstAttributeInstance(MapOutputName::class)) {
                $mappers[$property->getName()] = $attribute->output;
            }
        }

        return $mappers;
    }
}
