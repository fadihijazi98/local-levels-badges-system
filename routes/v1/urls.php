<?php
/**
 * Script to register all app URLs.
 * Register URLs by using `Route` component.
 * All URLs in this script are in version 1, so should have suffix "api/v1".
 */
use Components\Route;

Route::GET('students', \Controllers\StudentsController::class);