<?php

namespace Mementohub\Data\Parsers\Class;

use Mementohub\Data\Contracts\Parser;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Factories\Casters;

class DataClassParser implements Parser
{
    /** @var array<string, Caster[]> */
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
        foreach ($this->casters as $property => $casters) {
            if (isset($data[$property])) {
                foreach ($casters as $caster) {
                    $data[$property] = $caster->handle($data[$property], $data);
                }
                $processed[] = $data[$property];
            } else {
                // if (array_key_exists($property, $this->class->defaults)) {
                $processed[] = $this->class->defaults[$property];
                // }
            }
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
