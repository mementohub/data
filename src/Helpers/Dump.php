<?php

namespace Mementohub\Data\Helpers;

class Dump
{
    public static function var(mixed $var, int $depth = 1): string
    {
        if (is_object($var)) {
            return static::object($var, $depth);
        }

        if (is_array($var)) {
            return static::indent(static::collection($var, $depth));
        }

        if (is_numeric($var) || is_bool($var) || is_string($var)) {
            return json_encode($var, JSON_PRETTY_PRINT);
        }

        return static::type($var);
    }

    protected static function type(mixed $var): string
    {
        if (is_array($var)) {
            return 'array('.count($var).')';
        }

        if (is_object($var)) {
            return get_class($var);
        }

        return gettype($var);
    }

    protected static function object(object $var, int $depth): string
    {
        $reflection = new \ReflectionObject($var);
        if ($reflection->isEnum()) {
            return $reflection->getName().'::'.$var->name.' => '.json_encode($var->value);
        }

        $output = $reflection->getName();

        if ($depth === 0) {
            return $output;
        }

        $vars = '';
        foreach ($reflection->getProperties() as $property) {
            $vars .= "\n".'"'.$property->getName().'": '.static::indent(static::var($property->getValue($var), $depth - 1));
        }

        $output .= ' {'.static::indent($vars)."\n}";

        return $output;
    }

    protected static function collection(array $var, int $depth): string
    {
        $output = 'array('.count($var).')';

        if ($depth === 0) {
            return $output;
        }

        $output .= ' [';

        foreach ($var as $key => $value) {
            $output .= "\n ".json_encode($key).' => '.static::var($value, $depth - 1);
        }

        return $output."\n]";
    }

    protected static function indent(string $output): string
    {
        return preg_replace('/\s\s([\]}])$/', '$1', implode("\n    ", explode("\n", $output)));
    }
}
