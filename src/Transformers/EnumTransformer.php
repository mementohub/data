<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Contracts\Transformer;

class EnumTransformer implements Transformer
{
    public function handle(mixed $value): mixed
    {
        return $value->value;
    }
}
