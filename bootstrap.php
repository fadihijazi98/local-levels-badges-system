<?php
/**
 * bootstrap script starting with requires autoload psr-4
 * then we will not use `require` or `require_once` or `include` anymore
 */
require 'vendor/autoload.php';

/**
 * use Dotenv &
 * load environment variables.
 * use `$_ENV` to access variables.
 * (safeLoad) to skip exceptions if `.env` not exist
 */
use Dotenv\Dotenv;
Dotenv::createImmutable(__DIR__)->safeLoad();

/**
 * Bootstrapping Eloquent (ORM)
 */
use Illuminate\Database\Capsule\Manager;

$capsule = new Manager;
$capsule->addConnection([
    'driver' => $_ENV['DATABASE_DRIVER'],
    'host' => $_ENV['DATABASE_HOST'],
    'database' => $_ENV['DATABASE_NAME'],
    'username' => $_ENV['DATABASE_USER'],
    'password' => $_ENV['DATABASE_PASSWORD'],
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Manager::enableQueryLog();

