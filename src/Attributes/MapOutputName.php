<?php

namespace Mementohub\Data\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MapOutputName
{
    public function __construct(
        public string|int $output
    ) {}
}
