<?php

namespace Mementohub\Data\Casters;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataProperty;
use Mementohub\Data\Factories\Parsers;
use Traversable;

class CollectionCaster implements Caster
{
    /** @var Parser[] */
    protected readonly array $parsers;

    protected readonly ?string $type;

    public function __construct(
        protected readonly DataProperty $property,
        protected readonly ?string $class
    ) {
        if ($class) {
            $this->parsers = Parsers::for($class);
        } else {
            $this->parsers = [];
        }

        $this->type = $this->property->getType()->firstOf(Traversable::class);
    }

    public function handle(mixed $value, array $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (count($this->parsers) == 0) {
            if (is_null($this->type)) {
                return $value;
            }

            return new $this->type($value);
        }

        $collection = [];
        foreach ($value as $key => $item) {
            foreach ($this->parsers as $parser) {
                $item = $parser->handle($item, $context);
            }
            $collection[$key] = $item;
        }

        if (is_null($this->type)) {
            return $collection;
        }

        return new $this->type($collection);
    }
}
