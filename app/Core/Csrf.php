<?php
namespace App\Core;

/** Защита от CSRF: токен в сессии и скрытое поле в формах. */
class Csrf
{
    public static function token(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    /** HTML скрытого поля для формы. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    /** Проверяет токен POST-запроса; при ошибке отвечает 419. */
    public static function verify(): void
    {
        $sent = $_POST['_csrf'] ?? '';
        if (!is_string($sent) || !hash_equals(self::token(), $sent)) {
            http_response_code(419);
            View::render('error', ['code' => 419, 'message' => 'Сессия формы устарела. Обновите страницу и повторите.']);
            exit;
        }
    }
}
