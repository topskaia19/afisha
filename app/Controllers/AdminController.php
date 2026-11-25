<?php
namespace App\Controllers;

use App\Core\{Auth, Validator, View};
use App\Models\{Category, Event, User};
use PDOException;

/** Панель администратора: пользователи, события, категории. Все методы требуют роль admin. */
class AdminController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /** Сводка. */
    public function dashboard(): void
    {
        View::render('admin/dashboard', ['stats' => Event::stats(), 'users' => User::count(), 'categories' => count(Category::all())]);
    }

    public function users(): void
    {
        View::render('admin/users', ['users' => User::all()]);
    }

    public function setRole(string $id): void
    {
        $role = $_POST['role'] ?? '';
        if ((int)$id === Auth::user()['id'] || !in_array($role, ['user', 'admin'], true)) {
            flash('Нельзя изменить собственную роль или задать неверную.', 'danger');
        } else {
            User::setRole((int)$id, $role);
            flash('Роль обновлена.');
        }
        redirect('/admin/users');
    }

    public function deleteUser(string $id): void
    {
        if ((int)$id === Auth::user()['id']) {
            flash('Нельзя удалить свой аккаунт.', 'danger');
        } else {
            User::delete((int)$id);
            flash('Пользователь удалён вместе с его событиями.');
        }
        redirect('/admin/users');
    }

    public function adminForm(): void
    {
        View::render('admin/new_admin', ['old' => [], 'errors' => []]);
    }

    /** Создание учётной записи с ролью администратора. */
    public function createAdmin(): void
    {
        $name = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $pass = (string)($_POST['password'] ?? '');
        $err = [];
        if (!preg_match('/^[\p{L}\d_]{3,30}$/u', $name)) {
            $err['username'] = 'Логин — 3–30 символов: буквы, цифры, подчёркивание.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Введите корректный email.';
        }
        if ($m = Validator::password($pass, (string)($_POST['password2'] ?? ''))) {
            $err['password'] = $m;
        }
        if (!$err && User::exists($email, $name)) {
            $err['email'] = 'Пользователь с таким email или логином уже есть.';
        }
        if ($err) {
            View::render('admin/new_admin', ['old' => ['username' => $name, 'email' => $email], 'errors' => $err]);
            return;
        }
        User::create($name, $email, $pass, 'admin');
        flash('Администратор «' . $name . '» создан.');
        redirect('/admin/users');
    }

    public function events(): void
    {
        View::render('admin/events', ['events' => Event::search([], false)]);
    }

    public function editEvent(string $id): void
    {
        $e = Event::find((int)$id) ?? $this->notFound();
        $e['event_date'] = str_replace(' ', 'T', $e['event_date']);
        View::render('admin/edit_event', ['id' => $e['id'], 'old' => $e, 'errors' => [], 'categories' => Category::all()]);
    }

    public function updateEvent(string $id): void
    {
        Event::find((int)$id) ?? $this->notFound();
        [$data, $errors] = Validator::event($_POST, false);
        if ($errors) {
            View::render('admin/edit_event', ['id' => (int)$id, 'old' => $_POST, 'errors' => $errors, 'categories' => Category::all()]);
            return;
        }
        Event::update((int)$id, $data);
        flash('Изменения сохранены.');
        redirect('/admin/events');
    }

    public function approveEvent(string $id): void
    {
        Event::setStatus((int)$id, 'published');
        flash('Событие опубликовано.');
        redirect('/admin/events');
    }

    public function deleteEvent(string $id): void
    {
        Event::delete((int)$id);
        flash('Событие удалено.');
        redirect('/admin/events');
    }

    public function categories(): void
    {
        View::render('admin/categories', ['categories' => Category::withCounts()]);
    }

    public function addCategory(): void
    {
        $name = trim((string)($_POST['name'] ?? ''));
        if (mb_strlen($name) < 2 || mb_strlen($name) > 40) {
            flash('Название категории — от 2 до 40 символов.', 'danger');
        } else {
            try {
                Category::create($name);
                flash('Категория добавлена.');
            } catch (PDOException) {
                flash('Такая категория уже существует.', 'danger');
            }
        }
        redirect('/admin/categories');
    }

    public function deleteCategory(string $id): void
    {
        try {
            Category::delete((int)$id);
            flash('Категория удалена.');
        } catch (PDOException) {
            flash('Нельзя удалить категорию, в которой есть события.', 'danger');
        }
        redirect('/admin/categories');
    }

    private function notFound(): never
    {
        http_response_code(404);
        View::render('error', ['code' => 404, 'message' => 'Запись не найдена.']);
        exit;
    }
}
