<?php
namespace App\Controllers;

use App\Core\{Auth, Validator, View};
use App\Models\{Category, Event};

/** Поиск, просмотр и создание событий. */
class EventController
{
    /** Форма поиска + результаты (публично). */
    public function index(): void
    {
        $f = array_map(fn($v) => is_string($v) ? trim($v) : '', $_GET);
        View::render('events/index', ['events' => Event::search($f), 'categories' => Category::all(), 'f' => $f]);
    }

    public function show(string $id): void
    {
        $e = Event::find((int)$id);
        $canSee = $e && ($e['status'] === 'published' || Auth::isAdmin() || (Auth::check() && Auth::user()['id'] == $e['user_id']));
        if (!$canSee) {
            http_response_code(404);
            View::render('error', ['code' => 404, 'message' => 'Событие не найдено.']);
            return;
        }
        View::render('events/show', ['e' => $e]);
    }

    public function createForm(): void
    {
        Auth::requireLogin();
        View::render('events/create', ['old' => [], 'errors' => [], 'categories' => Category::all()]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        [$data, $errors] = Validator::event($_POST);
        if ($errors) {
            View::render('events/create', ['old' => $_POST, 'errors' => $errors, 'categories' => Category::all()]);
            return;
        }
        $status = Auth::isAdmin() ? 'published' : 'pending';
        $id = Event::create($data, (int)Auth::user()['id'], $status);
        flash($status === 'published' ? 'Событие опубликовано.' : 'Событие отправлено на модерацию. Оно появится в афише после проверки.');
        redirect('/events/' . $id);
    }
}
