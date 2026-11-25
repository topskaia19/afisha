<?php

namespace App\Models;

use App\Core\Database as DB;

/** Модель пользователя. */
class User
{
    public static function find(int $id): ?array
    {
        return DB::query(
            'SELECT * FROM users WHERE id = ?',
            [$id]
        )->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        return DB::query(
            'SELECT * FROM users WHERE email = ?',
            [mb_strtolower($email)]
        )->fetch() ?: null;
    }

    /** Проверяет занятость email/логина. */
    public static function exists(string $email, string $username): bool
    {
        return (bool) DB::query(
            'SELECT 1 FROM users WHERE email = ? OR username = ?',
            [mb_strtolower($email), $username]
        )->fetch();
    }

    /** Создаёт пользователя и возвращает его ID. */
    public static function create(
        string $username,
        string $email,
        string $password,
        string $role = 'user'
    ): int {
        return (int) DB::query(
            'INSERT INTO users (username, email, password_hash, role)
             VALUES (?, ?, ?, ?)
             RETURNING id',
            [
                $username,
                mb_strtolower($email),
                password_hash($password, PASSWORD_DEFAULT),
                $role
            ]
        )->fetchColumn();
    }

    public static function all(): array
    {
        return DB::query(
            'SELECT id, username, email, role, created_at
             FROM users ORDER BY id'
        )->fetchAll();
    }

    public static function setRole(int $id, string $role): void
    {
        DB::query('UPDATE users SET role = ? WHERE id = ?', [$role, $id]);
    }

    public static function delete(int $id): void
    {
        DB::query('DELETE FROM users WHERE id = ?', [$id]);
    }

    public static function setPassword(int $id, string $password): void
    {
        DB::query(
            'UPDATE users SET password_hash = ? WHERE id = ?',
            [password_hash($password, PASSWORD_DEFAULT), $id]
        );
    }

    /** Создаёт токен сброса пароля. */
    public static function createResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));

        DB::query('DELETE FROM password_resets WHERE user_id = ?', [$userId]);

        DB::query(
            'INSERT INTO password_resets (user_id, token_hash, expires_at)
             VALUES (?, ?, ?)',
            [
                $userId,
                hash('sha256', $token),
                date('Y-m-d H:i:s', time() + 3600)
            ]
        );

        return $token;
    }

    /** Возвращает ID пользователя по действующему токену. */
    public static function userIdByResetToken(string $token): ?int
    {
        $row = DB::query(
            'SELECT user_id FROM password_resets
             WHERE token_hash = ? AND expires_at > ?',
            [
                hash('sha256', $token),
                date('Y-m-d H:i:s')
            ]
        )->fetch();

        return $row ? (int) $row['user_id'] : null;
    }

    public static function deleteResetTokens(int $userId): void
    {
        DB::query(
            'DELETE FROM password_resets WHERE user_id = ?',
            [$userId]
        );
    }

    public static function count(): int
    {
        return (int) DB::query(
            'SELECT COUNT(*) FROM users'
        )->fetchColumn();
    }
}
