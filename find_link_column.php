<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

foreach (DB::select('SHOW TABLES') as $table) {
    $tableName = array_values((array) $table)[0];
    if (Schema::hasColumn($tableName, 'link')) {
        echo $tableName . PHP_EOL;
    }
}
