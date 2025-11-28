<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Factories\TransformerFactory;

class RecursiveTransformer implements Transformer
{
    protected ?Transformer $transformer;

    public function __construct(
        protected readonly string $class,
    ) {}

    public function handle(mixed $value): mixed
    {
        return $this->transformer()?->handle($value);
    }

    protected function transformer(): ?Transformer
    {
        return $this->transformer ??= TransformerFactory::for($this->class);
    }
}
