<?php

namespace Mementohub\Data\Contracts;

interface Transformer
{
    public function handle(mixed $data): mixed;
}
