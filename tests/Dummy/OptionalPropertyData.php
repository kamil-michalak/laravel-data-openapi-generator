<?php

namespace Xolvio\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OptionalPropertyData extends Data
{
    public function __construct(
        public string $name,
        public bool|Optional $flag = new Optional(),
        public ReturnData|Optional $single = new Optional(),
        public ReturnData|ContentTypeData|Optional $action = new Optional(),
    ) {}
}
