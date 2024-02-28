<?php

namespace Application\GraphQL;

use GraphQL\Type\Definition\Type;

class SchemaBuilder
{
    static function buildField(
        string $fieldName,
        Type $type,
        callable $resolver,
        array $args = [],
        string $description = ''
    ): array {
        return [
            $fieldName => [
                "type" => $type,
                "resolve" => $resolver,
                "description" => !empty($description) ? $description : $fieldName . ' Field',
                "args" => $args ?? $args || []
            ]
        ];
    }
}
