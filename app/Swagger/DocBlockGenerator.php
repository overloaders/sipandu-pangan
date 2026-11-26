<?php

namespace App\Swagger;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Generator as OpenApiGenerator;

/**
 * Keeps swagger-php's default analyser (docblock + attribute annotations).
 *
 * l5-swagger unconditionally overrides the generator's analyser with an
 * attribute-only one whenever `scanOptions.analyser` is null (see
 * L5Swagger\Generator::setAnalyser). This project documents its API with
 * docblock annotations, so that forced override is ignored here and
 * swagger-php falls back to its default analyser, which reads both styles.
 *
 * Kept as a plain class so it can be referenced from config via the
 * `generator_factory` option (a class name is config-cache safe, unlike the
 * object instances that break `php artisan config:cache`).
 *
 * NOTE: never write OA-prefixed tags in this file's comments - swagger-php
 * scans everything under app/ and will try to parse them as annotations.
 */
class DocBlockGenerator extends OpenApiGenerator
{
    public function setAnalyser(?AnalyserInterface $analyser): OpenApiGenerator
    {
        // Intentionally ignored: keep swagger-php's default analyser.
        return $this;
    }
}
