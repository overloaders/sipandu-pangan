<?php

namespace App\Swagger;

use L5Swagger\CustomGeneratorInterface;
use OpenApi\Generator as OpenApiGenerator;

class CustomGenerator implements CustomGeneratorInterface
{
    public function create(): OpenApiGenerator
    {
        return new DocBlockGenerator();
    }
}
