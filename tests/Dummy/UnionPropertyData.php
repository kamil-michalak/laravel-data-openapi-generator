<?php

namespace Xolvio\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;

class UnionPropertyData extends Data
{
    public function __construct(
        public ReturnData|ContentTypeData $action,
    ) {}
}
