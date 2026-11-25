<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\{Category, Event};

/** Публичная главная страница: данные формируются из БД. */
class HomeController
{
    public function index(): void
    {
        View::render('home/index', [
            'events' => Event::upcoming(6),
            'categories' => Category::withCounts(),
            'stats' => Event::stats(),
        ]);
    }
}
