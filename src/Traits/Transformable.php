<?php

namespace Mementohub\Data\Traits;

use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Factories\TransformerFactory;

trait Transformable
{
    public function toArray(): array
    {
        return $this->transformer()?->handle($this)
            ?? (array) $this;
    }

    protected function transformer(): ?Transformer
    {
        return TransformerFactory::for($this::class);
    }
}
