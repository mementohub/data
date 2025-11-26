<?php

namespace Mementohub\Data\Traits;

trait Cloneable
{
    public function clone(array $replace = []): static
    {
        return static::from($replace + $this->toArray());
    }
}
