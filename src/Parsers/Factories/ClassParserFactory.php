<?php

namespace Mementohub\Data\Parsers\Factories;

use Mementohub\Data\Parsers\Contracts\ClassParser;
use RuntimeException;

class ClassParserFactory
{
    protected static array $resolved_parsers = [];

    protected static array $pending_parsers = [];

    public static function for(string $class): ClassParser
    {
        return static::$resolved_parsers[$class] ??= static::resolveClassParser($class);
    }

    public static function resolveClassParser(string $class): ClassParser
    {
        if (in_array($class, static::$pending_parsers)) {
            throw new RuntimeException('Circular dependency detected');
        }

        static::$pending_parsers[] = $class;

        $parser = new ClassParserResolver($class)->resolve($class);

        static::$pending_parsers = array_diff(static::$pending_parsers, [$class]);

        return $parser;
    }
}
