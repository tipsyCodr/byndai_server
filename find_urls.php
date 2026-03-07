<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (DB::select('SHOW TABLES') as $table) {
    $tableName = array_values((array) $table)[0];
    $count = DB::table($tableName)->count();
    if ($count > 0) {
        $first = DB::table($tableName)->first();
        foreach ($first as $k => $v) {
            if (str_contains((string) $v, 'http')) {
                echo "Table: $tableName, Column: $k, Example: $v" . PHP_EOL;
            }
        }
    }
}
