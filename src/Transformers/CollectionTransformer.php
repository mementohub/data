<?php

namespace Mementohub\Data\Transformers;

use Generator;
use Illuminate\Contracts\Support\Arrayable;
use Mementohub\Data\Attributes\CollectionOf;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Factories\TransformerFactory;
use Traversable;

class CollectionTransformer implements Transformer
{
    protected readonly ?Transformer $transformer;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
    ) {
        $this->transformer = $this->resolveItemTransformer();

        $this->type = $this->property->getType()->firstOf(Traversable::class);
    }

    public function handle(mixed $value): mixed
    {
        if (is_null($this->transformer)) {
            if (is_null($this->type)) {
                return $value;
            }

            if (is_a($this->type, Arrayable::class, true)) {
                return $value->toArray();
            }

            if (is_a($this->type, Generator::class, true)) {
                return iterator_to_array($value);
            }
        }

        $transformed = [];
        foreach ($value as $key => $item) {
            $transformed[$key] = $this->transformer->handle($item);
        }

        return $transformed;
    }

    protected function resolveItemTransformer(): ?Transformer
    {
        if ($attribute = $this->property->getFirstAttributeInstance(CollectionOf::class)) {
            return TransformerFactory::for($attribute->class);
        }

        if ($type = $this->property->inferArrayTypeFromDocBlock()) {
            return TransformerFactory::for($type);
        }

        return null;
    }
}
