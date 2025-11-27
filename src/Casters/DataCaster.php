<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Exceptions\CastingException;
use Mementohub\Data\Factories\ParserFactory;

class DataCaster implements Caster
{
    protected readonly ?Parser $parser;

    public function __construct(
        public readonly DataProperty $property
    ) {
        $this->parser = ParserFactory::for($property->getType()->getMainType());
    }

    public function handle(mixed $value, array $context): mixed
    {
        if (is_null($this->parser)) {
            return $value;
        }

        try {
            return $this->parser->handle($value);
        } catch (\Throwable $t) {
            throw new CastingException('Unable to parse data property', $this->property, $value, $t);
        }
    }
}
