<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Factories\TransformerFactory;
use Mementohub\Data\Values\Optional;

class DataTransformer implements Transformer
{
    /** @var array<string, Transformer> */
    protected readonly array $transformers;

    protected readonly bool $doesntNeedTransformation;

    protected readonly bool $hasOptionals;

    public function __construct(
        protected readonly DataClass $class,
        protected readonly array $except = [],
    ) {
        $this->transformers = $this->resolveTransformers();
        $this->hasOptionals = $this->detectOptionals();
        $this->doesntNeedTransformation = (count(array_filter($this->transformers)) === 0)
            && ! $this->hasOptionals
            && ! count($this->except);
    }

    public function handle(mixed $data): mixed
    {
        if (is_null($data)) {
            return null;
        }

        if ($this->doesntNeedTransformation) {
            return (array) $data;
        }

        $transformed = [];
        foreach ($this->transformers as $property => $transformer) {
            if ($this->hasOptionals && ($data->$property instanceof Optional)) {
                continue;
            }

            if ($transformer === null) {
                $transformed[$property] = $data->$property;

                continue;
            }

            $transformed[$property] = $transformer->handle($data->$property);
        }

        return $transformed;
    }

    protected function resolveTransformers(): array
    {
        $transformers = [];
        foreach ($this->class->getProperties() as $property) {
            if (in_array($property->getName(), $this->except)) {
                continue;
            }
            $transformers[$property->getName()] = TransformerFactory::forProperty($property);
        }

        return $transformers;
    }

    protected function detectOptionals(): bool
    {
        foreach ($this->class->getProperties() as $property) {
            if ($property->allowsOptional()) {
                return true;
            }
        }

        return false;
    }
}
