<?php
/**
 * boostrap all project dependencies
 */
require_once 'bootstrap.php';

/**
 * disable annoying warnings if its implements in .env with true value
 */
if (($_ENV['DISABLE_WARNINGS'] ?? "false") == "true") {

    error_reporting(E_ERROR | E_PARSE);
}

/*
 * register all app URLs (according to RESTFUL-API standards)
 */
require_once "routes/v1/urls.php";

/*
 * handle coming request via Route component
 */
use Components\Route;
use CustomExceptions\ClientException;

$_VIEW = [];

try {
    $_VIEW[] = Route::handleRequest();

    if ($_ENV['GET_LOGGED_QUERIES'] == 'true') {

        $_VIEW[] = Manager::getQueryLog();
    }

} catch (Exception $e) {
    $debugModeIsActive = (($_ENV['DEBUG_MODE'] ?? "false") == "true");

    if($debugModeIsActive) {

        $_VIEW[] = $e->getMessage();
    } else{

        $_VIEW[] = "Internal server error.";
    }
} finally {
    require_once 'views/layout/app.php';
}