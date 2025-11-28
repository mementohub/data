<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Caster;
use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Exceptions\ParsingException;
use Mementohub\Data\Factories\CasterFactory;

class DataParser implements Parser
{
    /** @var array<string, Caster|null> */
    protected readonly array $casters;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->casters = $this->resolveCasters();
    }

    public function handle(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $processed = [];
        foreach ($this->casters as $property => $caster) {
            if (! array_key_exists($property, $value)) {
                if (array_key_exists($property, $this->class->defaults)) {
                    $processed[$property] = $this->class->defaults[$property];
                }

                continue;
            }

            if (is_null($caster)) {
                $processed[$property] = $value[$property];

                continue;
            }

            try {
                $processed[$property] = $caster->handle($value[$property], $value);
            } catch (\Throwable $t) {
                throw new ParsingException('Failed to cast property $'.$property, $this->class, $value, $t);
            }
        }

        try {
            return new $this->class->name(...$processed);
        } catch (\ArgumentCountError $t) {
            throw $this->argumentCountError($value, $processed, $t);
        } catch (\Throwable $t) {
            throw new ParsingException('Instantiation failure.', $this->class, $value, $t);
        }
    }

    protected function resolveCasters()
    {
        $casters = [];
        foreach ($this->class->getProperties() as $property) {
            $casters[$property->getName()] = CasterFactory::for($property);
        }

        return $casters;
    }

    protected function argumentCountError(array $data, array $processed, \ArgumentCountError $t): ParsingException
    {
        $expected = array_keys($this->class->getProperties());
        $actual = array_keys($processed);

        $message = [];
        foreach ($expected as $argument) {
            if (! in_array($argument, $actual)) {
                $message[] = '--- : '.$argument;

                continue;
            }
            $message[] = '    : '.$argument;
        }

        foreach (array_diff($actual, $expected) as $argument) {
            $message[] = '+++ : '.$argument;
        }

        return new ParsingException(
            'Failed to instantiate '.$this->class->getName().' with arguments '."\n".implode("\n", $message),
            $this->class,
            $data,
            $t
        );
    }
}
