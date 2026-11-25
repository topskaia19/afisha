<?php

namespace App\Models;

use App\Core\Database as DB;

/** Модель события афиши. */
class Event
{
    private const SELECT = 'SELECT e.*, c.name AS category, u.username AS author
        FROM events e
        JOIN categories c ON c.id = e.category_id
        JOIN users u ON u.id = e.user_id';

    public static function find(int $id): ?array
    {
        return DB::query(
            self::SELECT . ' WHERE e.id = ?',
            [$id]
        )->fetch() ?: null;
    }

    /**
     * Поиск по критериям.
     * @param array $f q, category, format, date_from, date_to, price_max, free
     * @param bool $onlyPublished показывать только опубликованные
     */
    public static function search(array $f, bool $onlyPublished = true): array
    {
        $where = [];
        $p = [];

        if ($onlyPublished) {
            $where[] = "e.status = 'published'";
        }

        if (($f['q'] ?? '') !== '') {
            $where[] = "(e.title ILIKE ? ESCAPE E'\\\\'
                OR e.description ILIKE ? ESCAPE E'\\\\')";

            $escaped = str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\\%', '\\_'],
                $f['q']
            );

            $like = '%' . $escaped . '%';
            array_push($p, $like, $like);
        }

        if ((int)($f['category'] ?? 0) > 0) {
            $where[] = 'e.category_id = ?';
            $p[] = (int)$f['category'];
        }

        if (in_array($f['format'] ?? '', ['online', 'offline'], true)) {
            $where[] = 'e.format = ?';
            $p[] = $f['format'];
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['date_from'] ?? '')) {
            $where[] = 'e.event_date >= ?';
            $p[] = $f['date_from'] . ' 00:00';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['date_to'] ?? '')) {
            $where[] = 'e.event_date <= ?';
            $p[] = $f['date_to'] . ' 23:59';
        }

        if (!empty($f['free'])) {
            $where[] = 'e.price = 0';
        } elseif (is_numeric($f['price_max'] ?? null)) {
            $where[] = 'e.price <= ?';
            $p[] = (float)$f['price_max'];
        }

        $sql = self::SELECT
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY e.event_date';

        return DB::query($sql, $p)->fetchAll();
    }

    /** Ближайшие опубликованные события. */
    public static function upcoming(int $limit): array
    {
        $limit = max(1, $limit);

        return DB::query(
            self::SELECT
            . " WHERE e.status = 'published'
                AND e.event_date >= ?
                ORDER BY e.event_date
                LIMIT " . $limit,
            [date('Y-m-d H:i')]
        )->fetchAll();
    }

    /** Данные для блока статистики на главной. */
    public static function stats(): array
    {
        return [
            'events' => (int) DB::query(
                "SELECT COUNT(*) FROM events WHERE status = 'published'"
            )->fetchColumn(),

            'free' => (int) DB::query(
                "SELECT COUNT(*) FROM events
                 WHERE status = 'published' AND price = 0"
            )->fetchColumn(),

            'online' => (int) DB::query(
                "SELECT COUNT(*) FROM events
                 WHERE status = 'published' AND format = 'online'"
            )->fetchColumn(),

            'pending' => (int) DB::query(
                "SELECT COUNT(*) FROM events WHERE status = 'pending'"
            )->fetchColumn(),
        ];
    }

    /** Создаёт событие и возвращает его ID. */
    public static function create(array $d, int $userId, string $status): int
    {
        return (int) DB::query(
            'INSERT INTO events
                (title, description, category_id, event_date, format,
                 city, price, age_limit, user_id, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             RETURNING id',
            [
                $d['title'],
                $d['description'],
                $d['category_id'],
                $d['event_date'],
                $d['format'],
                $d['city'],
                $d['price'],
                $d['age_limit'],
                $userId,
                $status
            ]
        )->fetchColumn();
    }

    public static function update(int $id, array $d): void
    {
        DB::query(
            'UPDATE events
             SET title = ?, description = ?, category_id = ?,
                 event_date = ?, format = ?, city = ?, price = ?,
                 age_limit = ?
             WHERE id = ?',
            [
                $d['title'],
                $d['description'],
                $d['category_id'],
                $d['event_date'],
                $d['format'],
                $d['city'],
                $d['price'],
                $d['age_limit'],
                $id
            ]
        );
    }

    public static function setStatus(int $id, string $status): void
    {
        DB::query(
            'UPDATE events SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    public static function delete(int $id): void
    {
        DB::query('DELETE FROM events WHERE id = ?', [$id]);
    }
}
