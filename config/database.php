<?php

function getDatabaseConfig():array
{
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=localhost:3306;dbname=web_todolist_test",
                "username" => "root",
                "password" => ""
            ],
            "prod" => [
                "url" => "mysql:host=localhost:3306;dbname=web_todolist",
                "username" => "root",
                "password" => ""
            ]
        ]
    ];
}