<?php

$localConfig = __DIR__ . '/config.local.php';

$local = file_exists($localConfig)
    ? require $localConfig
    : [];

return array_merge([
    'dsn' => 'pgsql:host=localhost;port=5432;dbname=afisha',
    'user' => 'postgres',
    'pass' => '',
    'debug_reset_link' => true,
], $local);