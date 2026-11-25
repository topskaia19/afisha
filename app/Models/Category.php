<?php
namespace App\Models;

use App\Core\Database as DB;

/** Модель категории событий. */
class Category
{
    public static function all(): array
    {
        return DB::query('SELECT * FROM categories ORDER BY name')->fetchAll();
    }

    /** Категории с числом опубликованных событий. */
    public static function withCounts(): array
    {
        return DB::query("SELECT c.id, c.name, COUNT(e.id) AS cnt FROM categories c
            LEFT JOIN events e ON e.category_id = c.id AND e.status = 'published'
            GROUP BY c.id ORDER BY cnt DESC, c.name")->fetchAll();
    }

    public static function exists(int $id): bool
    {
        return (bool)DB::query('SELECT 1 FROM categories WHERE id = ?', [$id])->fetch();
    }

    public static function create(string $name): void
    {
        DB::query('INSERT INTO categories (name) VALUES (?)', [$name]);
    }

    public static function delete(int $id): void
    {
        DB::query('DELETE FROM categories WHERE id = ?', [$id]);
    }
}
