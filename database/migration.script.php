<?php
/**
 * This script is run independently of `index.php`
 */
require 'bootstrap.php';

use Illuminate\Database\Capsule\Manager;

$migrations = glob(__DIR__ . "/migrations/*.table.php");

foreach ($migrations as $migration)
{
    $_ = explode('/', $migration);
    $scriptName = array_pop($_);

    /**
     * $scriptName should be ($dateCreated).($tableName).table.php
     */
    $tableName = explode('.', $scriptName)[1];

    if (Manager::schema()->hasTable("$tableName"))
    {
        echo "$tableName table is already migrated.\n";
        continue;
    }

    require_once $migration;
    echo "$scriptName script has been executed successfully. \n";
}
