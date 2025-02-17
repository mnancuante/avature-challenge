<?php
// config/dependencies.php

// use DI\Container;
// use DI\ContainerBuilder;
// use App\Controllers\JobController;
// use App\Controllers\ResponseController;
// use App\Services\ExternalJobService;
// use App\Services\JobService;
// use App\StandardResponse\StandardJobOffer;
// use App\Repositories\JobRepository;
// use App\Repositories\ExternalJobRepository;
// use App\Database\Database;

// return [
//     PDO::class => function () {
//         $config = require __DIR__ . '/config.php';
//         $dbConfig = $config['db'];

//         $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";
//         return new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
//             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//             PDO::ATTR_EMULATE_PREPARES => false,
//         ]);
//     },

//     Database::class => DI\autowire()->constructor(DI\get(PDO::class)),
//     JobRepository::class => DI\autowire()->constructor(DI\get(Database::class)),
//     ExternalJobRepository::class => DI\autowire()->constructor('http://localhost:8081/jobs'),
//     ExternalJobService::class => DI\autowire()->constructor(DI\get(ExternalJobRepository::class)),
//     JobService::class => DI\autowire()->constructor(DI\get(JobRepository::class), DI\get(ExternalJobService::class)),
//     JobController::class => DI\autowire()->constructor(DI\get(JobService::class)),
//     ResponseController::class => \DI\autowire(),
// ];

// config/dependencies.php

use DI\Container;
use DI\ContainerBuilder;
use App\Controllers\JobController;
use App\Controllers\ResponseController;
use App\Services\ExternalJobService;
use App\Services\JobService;
use App\StandardResponse\StandardJobOffer;
use App\Repositories\JobRepository;
use App\Repositories\ExternalJobRepository;
use App\Database\Database;
use App\Services\MailService;

return [
    PDO::class => function () {
        $config = require __DIR__ . '/config.php';
        $dbConfig = $config['db'];

        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";
        return new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    },

    Database::class => DI\autowire()->constructor(DI\get(PDO::class)),
    JobRepository::class => DI\autowire()->constructor(DI\get(Database::class)),
    ExternalJobRepository::class => DI\autowire()->constructor('http://localhost:8081/jobs'),
    ExternalJobService::class => DI\autowire()->constructor(DI\get(ExternalJobRepository::class)),
    JobService::class => DI\autowire()->constructor(DI\get(JobRepository::class), DI\get(ExternalJobService::class)),
    JobController::class => DI\autowire()->constructor(DI\get(JobService::class)),
    ResponseController::class => \DI\autowire(),
    MailService::class => \DI\autowire()
];

