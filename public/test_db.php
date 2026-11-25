<?php

require_once __DIR__ . '/../bootstrap.php';

try {
    $pdo = \App\Core\Database::pdo();

    echo "Подключение к PostgreSQL успешно!";
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}
