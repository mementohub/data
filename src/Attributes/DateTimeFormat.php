<?php

namespace Mementohub\Data\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateTimeFormat
{
    public function __construct(
        public readonly ?string $input = null,
        public readonly ?string $output = null,
    ) {}
}
