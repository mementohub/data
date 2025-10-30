<?php

namespace Mementohub\Data\Casters;

use DateTimeInterface;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;

class DateTimeCaster implements Caster
{
    protected static array $cached = [];

    protected readonly string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $format = null,
        protected readonly bool $cacheable = false
    ) {
        $this->type = $this->property->getType()->getName();
    }

    public function cast(mixed $value, array $context): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (! is_string($value)) {
            return null;
        }

        if ($this->cacheable) {
            return self::$cached[$value.':'.$this->format] ??= $this->resolveValue($value);
        }

        return $this->resolveValue($value);
    }

    protected function resolveValue(string $value): DateTimeInterface
    {
        if ($this->format) {
            return $this->type::createFromFormat($this->format, $value);
        }

        return new $this->type($value);
    }

    protected function resolveType(): string
    {
        $type = $this->property->getType()->firstOf(DateTimeInterface::class);

        if ($type === null) {
            throw new \RuntimeException('Unable to resolve DateTimeInterface type');
        }

        return $type;
    }
}
