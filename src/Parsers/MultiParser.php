<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;

class MultiParser implements Parser
{
    public function __construct(
        public readonly DataClass $class,
        /** @var Parser[] */
        protected readonly array $parsers
    ) {}

    public function handle(mixed $value): mixed
    {
        foreach ($this->parsers as $parser) {
            $value = $parser->handle($value);
        }

        return $value;
    }
}
