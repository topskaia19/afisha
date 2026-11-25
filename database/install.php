<?php

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$pdo = Database::pdo();

// Создаём таблицы, если их ещё нет
$pdo->exec(file_get_contents(__DIR__ . '/schema.sql'));

if ((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {

    $pdo->beginTransaction();

    try {
        // Создаём пользователей
        $ins = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role)
             VALUES (?, ?, ?, ?)'
        );

        $ins->execute([
            'admin',
            'admin@afisha.local',
            password_hash('Admin12345', PASSWORD_DEFAULT),
            'admin'
        ]);

        $ins->execute([
            'anna',
            'anna@afisha.local',
            password_hash('User12345', PASSWORD_DEFAULT),
            'user'
        ]);

        // Получаем ID пользователей
        $adminId = (int)$pdo->query(
            "SELECT id FROM users WHERE username = 'admin'"
        )->fetchColumn();

        $userId = (int)$pdo->query(
            "SELECT id FROM users WHERE username = 'anna'"
        )->fetchColumn();

        // Создаём категории
        $categories = [
            'Концерты',
            'Театр',
            'Выставки',
            'Технологии',
            'Спорт',
            'Кино'
        ];

        $categoryInsert = $pdo->prepare(
            'INSERT INTO categories (name) VALUES (?)'
        );

        foreach ($categories as $category) {
            $categoryInsert->execute([$category]);
        }

        // Получаем ID категорий
        $categoryIds = [];

        foreach ($categories as $category) {
            $stmt = $pdo->prepare(
                'SELECT id FROM categories WHERE name = ?'
            );

            $stmt->execute([$category]);
            $categoryIds[$category] = (int)$stmt->fetchColumn();
        }

        // Создаём тестовые события
        $eventInsert = $pdo->prepare(
            'INSERT INTO events
            (title, description, category_id, event_date, format,
             city, price, age_limit, user_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $date = fn(int $days, string $time) =>
            date('Y-m-d', strtotime("+$days days")) . ' ' . $time;

        $events = [
            [
                'Вечер джаза в филармонии',
                'Живой концерт джазового квартета: классические стандарты и авторские композиции.',
                'Концерты', 3, '19:00', 'offline', 'Кишинёв', 250, 12, $adminId, 'published'
            ],
            [
                'Ромео и Джульетта',
                'Новая постановка классической трагедии на сцене национального театра.',
                'Театр', 7, '18:30', 'offline', 'Кишинёв', 180, 16, $adminId, 'published'
            ],
            [
                'Выставка современной графики',
                'Работы молодых художников региона: плакат, иллюстрация, цифровое искусство.',
                'Выставки', 10, '10:00', 'offline', 'Бельцы', 0, 0, $adminId, 'published'
            ],
            [
                'Конференция по веб-разработке',
                'Доклады о backend, безопасности и архитектуре приложений. Трансляция онлайн.',
                'Технологии', 14, '11:00', 'online', null, 0, 0, $userId, 'published'
            ],
            [
                'Городской забег 10 км',
                'Массовый забег по центру города. Регистрация на месте.',
                'Спорт', 21, '09:00', 'offline', 'Кишинёв', 100, 0, $userId, 'published'
            ],
            [
                'Ночь короткометражек',
                'Показ лучших студенческих короткометражных фильмов с обсуждением.',
                'Кино', 5, '20:00', 'offline', 'Кишинёв', 60, 16, $userId, 'pending'
            ],
        ];

        foreach ($events as $event) {
            [$title, $description, $category, $days, $time,
                $format, $city, $price, $age, $userId, $status] = $event;

            $eventInsert->execute([
                $title,
                $description,
                $categoryIds[$category],
                $date($days, $time),
                $format,
                $city,
                $price,
                $age,
                $userId,
                $status
            ]);
        }

        $pdo->commit();

        echo "База данных успешно заполнена!";

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

} else {
    echo "Пользователи уже существуют. Начальные данные не добавлялись.";
}

echo "\nАдминистратор: admin@afisha.local / Admin12345";
echo "\nПользователь: anna@afisha.local / User12345";
