<?php

namespace Mementohub\Data\Parsers\Class;

use Exception;
use Mementohub\Data\Contracts\Normalizer;
use Mementohub\Data\Entities\DataClass;
use Mementohub\Data\Parsers\Contracts\ClassParser;
use Throwable;

class NormalizerClassParser implements ClassParser
{
    /** @var Normalizer[] */
    protected readonly array $normalizers;

    protected ClassParser $next;

    public function __construct(
        public readonly DataClass $class
    ) {
        $this->normalizers = $this->resolveNormalizers();
    }

    public function parse(mixed $data): mixed
    {
        try {
            return $this->next->parse(
                $this->normalize($data)
            );
        } catch (Exception $e) {
            throw new Exception('Unable to normalize data', $e->getCode(), $e);
        }
    }

    public function then(ClassParser $next): ClassParser
    {
        $this->next = $next;

        return $this;
    }

    protected function normalize(mixed $data): ?array
    {
        foreach ($this->normalizers as $normalizer) {
            try {
                return $normalizer->normalize($data);
            } catch (Throwable $e) {
                continue;
            }
        }

        return null;
    }

    protected function resolveNormalizers(): array
    {
        $normalizers = [];

        foreach ($this->class->name::normalizers() as $normalizer) {
            if (is_string($normalizer) && class_exists($normalizer)) {
                $normalizer = new $normalizer;
            }

            if (is_a($normalizer, Normalizer::class, true)) {
                $normalizers[] = $normalizer;
            }
        }

        return $normalizers;
    }
}
