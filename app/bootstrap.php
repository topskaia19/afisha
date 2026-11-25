<?php
/** Общая инициализация: автозагрузка классов (App\ -> app/), вспомогательные функции. */
spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});
require __DIR__ . '/helpers.php';
