<?php

namespace Mementohub\Data\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StripValues
{
    protected readonly array $arguments;

    public function __construct(
        mixed ...$arguments
    ) {
        if (count($arguments) === 0) {
            $arguments = [null];
        }

        $this->arguments = $arguments;
    }
}
