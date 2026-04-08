<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MasterEventImage;

$images = MasterEventImage::limit(5)->get();
echo json_encode($images->toArray(), JSON_PRETTY_PRINT);
