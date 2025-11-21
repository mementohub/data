<?php

namespace Mementohub\Data\Parsers;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Factories\Casters;

class DataParser implements Parser
{
    /** @var Caster[] */
    protected array $casters = [];

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->resolveCasters();
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
        foreach ($this->class->getProperties() as $property) {
            $this->casters[$property->getName()] = Casters::for($property);
        }
    }
}
