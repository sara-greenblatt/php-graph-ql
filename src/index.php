<?php

declare(strict_types=1);

namespace Application;

require_once __DIR__ . '/../vendor/autoload.php';
include './BL/ArrayLogic.php';

use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Application\GraphQL\SchemaBuilder;

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Allow: GET, POST, HEAD');
header('Access-Control-Allow-Headers: Content-Type');

$dummyList = [
    [
        'user_id' => 1, 'userName' => 'user1', 'email' => '<EMAIL>', 'password' => '<PASSWORD>', 'created_at' => '2021-01-01 00:00:00'
    ],
    [
        'user_id' => 2, 'userName' => 'user2', 'email' => '<EMAIL>', 'password' => '<PASSWORD>', 'created_at' => '2021-01-01 00:00:00'
    ],
    [
        'user_id' => 3, 'userName' => 'user3', 'email' => '<EMAIL>', 'password' => '<PASSWORD>', 'created_at' => '2021-01-01 00:00:00'
    ]
];

$userType = new ObjectType([
    "name" => "User",
    "fields" => [
        "user_id" => [
            "description" => "User ID",
            "type" => Type::id()
        ],
        "userName" => [
            "description" => "User Name",
            "type" => Type::nonNull(Type::string())
        ],
        "email" => [
            "description" => "User Email",
            "type" => Type::nonNull(Type::string())
        ],
        "password" => [
            "description" => "User Password",
            "type" => Type::nonNull(Type::string())
        ],
        "created_at" => [
            "description" => "User Creation Date",
            "type" => Type::string() // Datetime
        ]
    ]
]);

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        ...SchemaBuilder::buildField(
            'users',
            Type::listOf($userType),
            fn () =>
            array_map(
                fn ($item) => [
                    ...$item,
                    "created_at" => date('m/d/Y', strtotime($item["created_at"]))
                ],
                $dummyList
            ),
            [],
            'Users List'
        ),
        ...SchemaBuilder::buildField(
            'user',
            $userType,
            function ($root, $args) use ($dummyList) {
                if (!empty($args["name"])) {
                    $filtered = array_filter($dummyList, fn ($item) => $item["userName"] == $args["name"]);
                    return !empty($filtered) ? reset($filtered) : null;
                }
                $filtered = array_filter($dummyList, fn ($item) => $item["user_id"] == $args["id"]);
                return !empty($filtered) ? reset($filtered) : null;
            },
            [
                'id' => ['type' => Type::id()],
                'name' => ['type' => Type::string()]
            ],
            'User by ID or Name'
        )
    ]
]);

$sorted_array = [20, 25, 30, 32, 34];

$mutationType = new ObjectType([
    "name" => "Mutation",
    "fields" => [
        "searchVal" => [
            "type" => Type::int(),
            "args" => [
                'sorted_array' => ['type' => Type::listOf(Type::int())],
                'value' => ['type' => Type::int()]
            ],
            "resolve" => fn ($root, $args) => binary_search($args['sorted_array'], $args['value'])
        ],
        "intToStr" => [
            "type" => Type::string(),
            "args" => [
                'array' => ['type' => Type::listOf(Type::int())],
            ],
            "resolve" => fn ($root, $args) =>  int_to_letter($args['array'])
        ]
    ]
]);

$schema = new Schema(
    [
        'query' => $queryType,
        'mutation' => $mutationType
    ]
);

$server = new StandardServer([
    'schema' => $schema,
    'debugFlag' => 8
]);

error_log('PHP server is running in the background');
return $server->handleRequest();