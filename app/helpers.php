<?php
/** Экранирует строку для вывода в HTML (защита от XSS). */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Перенаправляет на указанный адрес и завершает выполнение. */
function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

/**
 * Работа с flash-сообщениями.
 * @param string|null $msg  текст сообщения; null — получить и очистить накопленные
 * @param string      $type success|danger|info
 * @return array список [тип, текст]
 */
function flash(?string $msg = null, string $type = 'success'): array
{
    if ($msg !== null) {
        $_SESSION['flash'][] = [$type, $msg];
        return [];
    }
    $all = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $all;
}

/** Форматирует дату события: «5 ноября, 19:00». */
function fmt_date(string $dt): string
{
    $m = ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $t = strtotime($dt);
    return (int)date('j', $t) . ' ' . $m[(int)date('n', $t)] . ', ' . date('H:i', $t);
}

/** Форматирует цену. */
function fmt_price($p): string
{
    return (float)$p == 0 ? 'Бесплатно' : number_format((float)$p, 0, '', ' ') . ' MDL';
}
