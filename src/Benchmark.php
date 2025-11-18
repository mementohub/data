<?php

namespace Mementohub\Data;

use Closure;

class Benchmark
{
    public static array $results = [];

    public static function track(string $key, Closure $callback): void
    {
        $start = microtime(true);
        $callback();
        $end = microtime(true);

        $duration = ($end - $start) * 1000;
        self::$results[$key] = (self::$results[$key] ?? 0) + $duration;
    }

    public static function results(): array
    {
        return self::$results;
    }
}
