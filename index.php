<?php
/**
 * boostrap all project dependencies
 */
require_once 'bootstrap.php';

/*
 * define response to be always in JSON format (RESTful-API)
 */
header('Content-Type: application/json; charset=utf-8');

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

$response = [];

try {
    $response = Route::handleRequest();

    if ($_ENV['GET_LOGGED_QUERIES'] == 'true') {

        $response['sql_queries'] = Manager::getQueryLog();
    }

} catch (ClientException $e) {

    $response = [
        'data' => [
            'validation_error' => $e->getMessage()
        ],
        'status_code' => $e->getCode()
    ];

} catch (Exception $e) {
    // check if we in debug mode first, so we can clear what exactly the exception is.
    $debugModeIsActive = (($_ENV['DEBUG_MODE'] ?? "false") == "true");

    $response = [
        "error" => "Internal server error.",
        "status_code" => \Constants\StatusCodes::INTERNAL_ERROR
    ];

    if($debugModeIsActive) {
        $response['error'] = $e->getMessage();
    }

} finally {
    http_response_code($response['status_code']);
    echo json_encode($response);
}