<?php
namespace App\Core;

use App\Models\User;

/** Аутентификация и проверка прав на основе сессии. */
class Auth
{
    /** Возвращает текущего пользователя или null. */
    public static function user(): ?array
    {
        static $cache = false;
        if ($cache === false) {
            $cache = isset($_SESSION['uid']) ? User::find((int)$_SESSION['uid']) : null;
            if ($cache === null) {
                unset($_SESSION['uid']);
            }
        }
        return $cache;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }

    /** Входит в систему; пересоздаёт ID сессии (защита от фиксации сессии). */
    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['uid'] = $userId;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /** Требует вход; иначе перенаправляет на форму входа. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('Войдите в аккаунт, чтобы продолжить.', 'info');
            redirect('/login');
        }
    }

    /** Требует роль администратора. */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            View::render('error', ['code' => 403, 'message' => 'Доступ только для администраторов.']);
            exit;
        }
    }
}
