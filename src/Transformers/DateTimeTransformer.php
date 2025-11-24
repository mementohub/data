<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Attributes\DateTimeFormat;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataProperty;

class DateTimeTransformer implements Transformer
{
    protected readonly ?string $format;

    public function __construct(
        protected readonly DataProperty $property,
    ) {
        $this->format = $this->property
            ->getFirstAttributeInstance(DateTimeFormat::class)
            ?->output
            ?? DATE_ATOM;
    }

    public function handle(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format($this->format);
        }

        return null;
    }
}
