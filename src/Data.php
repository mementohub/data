<?php

namespace Mementohub\Data;

abstract class Data
{
    public function from(array $payload): static {}
}
