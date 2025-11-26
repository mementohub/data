<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Exceptions\CastingException;
use Mementohub\Data\Factories\ParserFactory;

class CollectionCaster implements Caster
{
    protected readonly ?Parser $parser;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $class = null
    ) {
        $this->parser = $class ? ParserFactory::for($class) : null;

        $this->type = $this->property->getType()->firstOf(\Traversable::class);
    }

    public function handle(mixed $value, array $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (is_null($this->parser)) {
            if (is_null($this->type)) {
                return $value;
            }

            try {
                return new $this->type($value);
            } catch (\Throwable $t) {
                throw new CastingException('Unable to instantiate collection of type '.$this->type, $this->property, $value, $t);
            }
        }

        try {
            $collection = [];
            foreach ($value as $key => $item) {
                $collection[$key] = $this->parser->handle($item, $context);
            }
        } catch (\Throwable $t) {
            throw new CastingException('Unable to parse item '.$key.' in collection', $this->property, $value, $t);
        }

        if (is_null($this->type)) {
            return $collection;
        }

        try {
            return new $this->type($collection);
        } catch (\Throwable $t) {
            throw new CastingException('Failed to instantiate collection of type '.$this->type, $this->property, $value, $t);
        }
    }
}
