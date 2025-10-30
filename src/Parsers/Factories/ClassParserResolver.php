<?php

namespace Mementohub\Data\Parsers\Factories;

use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Class\DataClassParser;
use Mementohub\Data\Parsers\Class\NormalizerClassParser;
use Mementohub\Data\Parsers\Class\PlainClassParser;
use Mementohub\Data\Parsers\Class\PlainClassWithDefaultsParser;
use Mementohub\Data\Parsers\Contracts\ClassParser;

class ClassParserResolver
{
    protected DataClass $class;

    public function __construct(string $class)
    {
        $this->class = new DataClass($class);
    }

    public function resolve(): ClassParser
    {
        if ($this->class->needsNormalizing()) {
            return new NormalizerClassParser($this->class)
                ->then($this->resolveClassParser());
        }

        return $this->resolveClassParser();
    }

    public function resolveClassParser(): ClassParser
    {
        if ($this->class->isPlainClass()) {
            return $this->resolvePlainClass();
        }

        return new DataClassParser($this->class);
    }

    protected function resolvePlainClass(): ClassParser
    {
        if (count($this->class->getNullDefaultableProperties()) > 0) {
            return new PlainClassWithDefaultsParser($this->class);
        }

        return new PlainClassParser($this->class);
    }
}
