<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            // Slim Settings
            'db' => [
                'driver' => 'mysql',
                'host' => 'host',
                'database' => 'name',
                'username' => 'username',
                'password' => '',
                'charset'   => '',
                'collation' => '',
                'prefix'    => '',
            ]
        ],
    ]);
};
