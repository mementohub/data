<?php

namespace Mementohub\Data\Transformers;

use Mementohub\Data\Attributes\DateTimeFormat;
use Mementohub\Data\Contracts\Transformer;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Exceptions\TransformingException;

class DateTimeTransformer implements Transformer
{
    protected readonly ?string $format;

    public function __construct(
        protected readonly DataProperty $property,
    ) {
        $this->format = $this->property
            ->getFirstAttributeInstance(DateTimeFormat::class)
            ->output
            ?? DATE_ATOM;
    }

    public function handle(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            try {
                return $value->format($this->format);
            } catch (\Throwable $t) {
                throw new TransformingException('Unable to format date using format '.$this->format."\n".$this->property, $value, $t);
            }
        }

        return null;
    }
}
