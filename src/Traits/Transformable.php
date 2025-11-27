<?php

namespace Mementohub\Data\Traits;

use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Factories\TransformerFactory;
use Mementohub\Data\Transformers\DataTransformer;

trait Transformable
{
    public function toArray(): array
    {
        return $this->transformer()?->handle($this)
            ?? (array) $this;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function except(array|string ...$properties): static
    {
        TransformerFactory::setExceptions($this, $properties);

        return $this;
    }

    protected function transformer(): ?Transformer
    {
        if ($exceptions = TransformerFactory::getExceptions($this)) {
            return new DataTransformer(
                new DataClass($this::class),
                $exceptions
            );
        }

        return TransformerFactory::for($this::class);
    }
}
