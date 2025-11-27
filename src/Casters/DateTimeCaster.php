<?php

namespace Mementohub\Data\Casters;

use DateTimeInterface;
use Mementohub\Data\Attributes\DateTimeFormat;
use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Exceptions\CastingException;

class DateTimeCaster implements Caster
{
    protected static array $cached = [];

    protected readonly string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected ?string $format = null,
        protected readonly bool $cacheable = true
    ) {
        $this->type = $this->property->getType()->getMainType();

        if (! $this->format) {
            $this->format = $this->property->getFirstAttributeInstance(DateTimeFormat::class)?->input;
        }
    }

    public function handle(mixed $value, array $context): ?DateTimeInterface
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
            try {
                return $this->type::createFromFormat($this->format, $value);
            } catch (\Throwable $t) {
                throw new CastingException('Unable to create '.$this->type.' from format '.$this->format, $this->property, $value, $t);
            }
        }

        try {
            return new $this->type($value);
        } catch (\Throwable $t) {
            throw new CastingException('Unable to create '.$this->type, $this->property, $value, $t);
        }
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
