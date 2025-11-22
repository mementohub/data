<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Factories\ParserFactory;

class GeneratorCaster implements Caster
{
    protected readonly ?Parser $parser;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $class
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

        foreach ($value as $item) {
            yield $this->parser->handle($item, $context);
        }
    }
}
