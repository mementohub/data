<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Exceptions\CastingException;
use Mementohub\Data\Factories\ParserFactory;

class GeneratorCaster implements Caster
{
    protected readonly ?Parser $parser;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $class = null
    ) {
        $this->parser = $class ? ParserFactory::for($class) : null;
    }

    public function handle(mixed $value, array $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (is_null($this->parser)) {
            return yield from $value;
        }

        foreach ($value as $key => $item) {
            try {
                yield $key => $this->parser->handle($item, $context);
            } catch (\Throwable $t) {
                throw new CastingException('Unable to parse item '.$key.' in generator', $this->property, $item, $t);
            }
        }
    }
}
