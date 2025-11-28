<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Factories\ParserFactory;

class RecursiveParser implements Parser
{
    protected ?Parser $parser;

    public function __construct(
        protected readonly string $class,
    ) {}

    public function handle(mixed $value): mixed
    {
        return $this->parser()?->handle($value);
    }

    protected function parser(): ?Parser
    {
        return $this->parser ??= ParserFactory::for($this->class);
    }
}
