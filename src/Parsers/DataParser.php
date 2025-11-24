<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Factories\CasterFactory;

class DataParser implements Parser
{
    /** @var array<string, Caster> */
    protected readonly array $casters;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->casters = $this->resolveCasters();
    }

    public function handle(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        $processed = [];
        foreach ($this->casters as $property => $caster) {
            if (! array_key_exists($property, $data)) {
                if (array_key_exists($property, $this->class->defaults)) {
                    $processed[$property] = $this->class->defaults[$property];
                }

                continue;
            }

            if (is_null($caster)) {
                $processed[$property] = $data[$property];

                continue;
            }

            $processed[$property] = $caster->handle($data[$property], $data);
        }

        return new $this->class->name(...$processed);
    }

    protected function resolveCasters()
    {
        $casters = [];
        foreach ($this->class->getProperties() as $property) {
            $casters[$property->getName()] = CasterFactory::for($property);
        }

        return $casters;
    }
}
