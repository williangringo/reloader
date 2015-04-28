<?php

$files = [
    'customers.php',
    'products.php',
    'orders.php',
    'categories.php', # Não queremos agora
];

$path = __DIR__;

foreach ($files as $file) {
    require_once $path .'/'. $file;
}
