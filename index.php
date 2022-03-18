<?php declare(strict_types = 1);

/* DEV ONLY */
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/vendor/autoload.php';

$database = new App\Database\Database("dev", "user", "password");
$testEntity = new App\Test\TestEntity($database);

$testEntity->testcolumn1 = 'testvalue1';
$testEntity->testcolumn2 = 'testvalue2';
$testEntity->save();

var_dump(App\Test\TestEntity::find($testEntity->getId()));
