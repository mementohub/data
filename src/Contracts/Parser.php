<?php

namespace Mementohub\Data\Contracts;

interface Parser
{
    public function handle(mixed $payload): mixed;
}
