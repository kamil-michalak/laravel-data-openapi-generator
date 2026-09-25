<?php

namespace Xolvio\OpenApiGenerator\Data;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Data as LaravelData;
use Spatie\LaravelData\Optional;

class Property extends Data
{
    public function __construct(
        protected string $name,
        public Schema $type,
        public bool $required = true,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int,self>
     */
    public static function fromDataClass(string $class): Collection
    {
        if (! is_a($class, LaravelData::class, true)) {
            throw new RuntimeException('Class does not extend LaravelData');
        }

        $reflection = new ReflectionClass($class);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        return self::collect(
            array_map(
                fn (ReflectionProperty $property) => self::fromProperty($property),
                $properties
            ),
            Collection::class
        );
    }

    public static function fromProperty(ReflectionProperty $reflection): self
    {
        return new self(
            name: $reflection->getName(),
            type: Schema::fromReflectionProperty($reflection),
            required: ! $reflection->getType()?->allowsNull() && ! self::isOptional($reflection),
        );
    }

    // `Foo|Optional` - the key may be missing from the output entirely, so it is never required
    protected static function isOptional(ReflectionProperty $reflection): bool
    {
        $type = $reflection->getType();
        if (! $type instanceof ReflectionUnionType) {
            return false;
        }

        foreach ($type->getTypes() as $item) {
            if ($item instanceof ReflectionNamedType && is_a($item->getName(), Optional::class, true)) {
                return true;
            }
        }

        return false;
    }
}
