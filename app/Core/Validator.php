<?php
namespace App\Core;

use App\Models\Category;
use DateTime;

/** Серверная валидация входных данных форм. */
class Validator
{
    /**
     * Валидация формы события.
     * @param array $d             данные формы (обычно $_POST)
     * @param bool  $mustBeFuture  требовать дату в будущем
     * @return array [очищенные данные, ошибки[поле => сообщение]]
     */
    public static function event(array $d, bool $mustBeFuture = true): array
    {
        $err = [];
        $title = trim((string)($d['title'] ?? ''));
        $desc = trim((string)($d['description'] ?? ''));
        $cat = (int)($d['category_id'] ?? 0);
        $format = (string)($d['format'] ?? '');
        $city = trim((string)($d['city'] ?? ''));
        $price = str_replace(',', '.', trim((string)($d['price'] ?? '0')));
        $age = (int)($d['age_limit'] ?? 0);
        $dateRaw = (string)($d['event_date'] ?? '');

        if (mb_strlen($title) < 3 || mb_strlen($title) > 100) {
            $err['title'] = 'Название — от 3 до 100 символов.';
        }
        if (mb_strlen($desc) < 10 || mb_strlen($desc) > 2000) {
            $err['description'] = 'Описание — от 10 до 2000 символов.';
        }
        if (!Category::exists($cat)) {
            $err['category_id'] = 'Выберите категорию из списка.';
        }
        if (!in_array($format, ['online', 'offline'], true)) {
            $err['format'] = 'Выберите формат: онлайн или офлайн.';
        }
        if ($format === 'offline' && ($city === '' || mb_strlen($city) > 60)) {
            $err['city'] = 'Для офлайн-события укажите город (до 60 символов).';
        }
        if (!is_numeric($price) || (float)$price < 0 || (float)$price > 100000) {
            $err['price'] = 'Цена — число от 0 до 100000.';
        }
        if (!in_array($age, [0, 6, 12, 16, 18], true)) {
            $err['age_limit'] = 'Выберите возрастное ограничение из списка.';
        }
        $dt = DateTime::createFromFormat('Y-m-d\TH:i', $dateRaw);
        if (!$dt || $dt->format('Y-m-d\TH:i') !== $dateRaw) {
            $err['event_date'] = 'Укажите дату и время события.';
        } elseif ($mustBeFuture && $dt <= new DateTime()) {
            $err['event_date'] = 'Дата должна быть в будущем.';
        }

        $clean = [
            'title' => $title, 'description' => $desc, 'category_id' => $cat, 'format' => $format,
            'city' => $format === 'offline' ? $city : null,
            'price' => is_numeric($price) ? (float)$price : 0, 'age_limit' => $age,
            'event_date' => $dt ? $dt->format('Y-m-d H:i') : '',
        ];
        return [$clean, $err];
    }

    /** Валидация пароля: минимум 8 символов, буква и цифра. */
    public static function password(string $pass, string $confirm): ?string
    {
        if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) {
            return 'Пароль — минимум 8 символов, должен содержать букву и цифру.';
        }
        return $pass === $confirm ? null : 'Пароли не совпадают.';
    }
}
