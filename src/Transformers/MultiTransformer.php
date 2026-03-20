<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Contracts\Transformer;

class MultiTransformer implements Transformer
{
    public function __construct(
        /** @var Transformer[] */
        protected readonly array $transformers
    ) {}

    public function handle(mixed $value): mixed
    {
        foreach ($this->transformers as $transformer) {
            $value = $transformer->handle($value);
        }

        return $value;
    }
}
