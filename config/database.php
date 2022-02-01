<?php

function getDatabaseConfig(): array {
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=localhost:3306;dbname=dbtesting",
                "username" => "root",
                "password" => "paramadaksa"
            ],
            "prod" => [
                "url" => "mysql:host=localhost:3306;dbname=dbapp",
                "username" => "root",
                "password" => "paramadaksa"
            ]
        ]
    ];
}
