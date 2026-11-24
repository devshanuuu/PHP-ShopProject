<?php
// Copy config.example.php to config.local.php to change your local settings.
$config = ['host'=>'127.0.0.1', 'port'=>3306, 'database'=>'everyday_shop', 'username'=>'root', 'password'=>''];
if (is_file(__DIR__ . '/config.local.php')) {
    $config = array_replace($config, require __DIR__ . '/config.local.php');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database'], (int)$config['port']);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
    http_response_code(503);
    exit('Database unavailable. Import database/schema.sql and check config.local.php. See README.md for setup.');
}
