<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Exceptions\TransformingException;
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

    public function handle(mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if ($this->doesntNeedTransformation) {
            return (array) $value;
        }

        $transformed = [];
        foreach ($this->transformers as $property => $transformer) {
            if ($this->hasOptionals && ($value->$property instanceof Optional)) {
                continue;
            }

            if ($transformer === null) {
                $transformed[$property] = $value->$property;

                continue;
            }

            try {
                $transformed[$property] = $transformer->handle($value->$property);
            } catch (\Throwable $t) {
                throw new TransformingException('Failed to transform property '."\n".$property, $value, $t);
            }
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
