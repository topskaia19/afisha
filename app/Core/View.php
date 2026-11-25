<?php
namespace App\Core;

/** Рендеринг представлений внутри общего макета. */
class View
{
    /**
     * @param string $view имя шаблона относительно app/Views (без .php)
     * @param array  $data переменные для шаблона
     */
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }
}
