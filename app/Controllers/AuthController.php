<?php
namespace App\Controllers;

use App\Core\{Auth, Validator, View};
use App\Models\User;

/** Регистрация, вход, выход, восстановление пароля. */
class AuthController
{
    public function registerForm(): void
    {
        View::render('auth/register', ['old' => [], 'errors' => []]);
    }

    public function register(): void
    {
        $name = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $pass = (string)($_POST['password'] ?? '');
        $err = [];
        if (!preg_match('/^[\p{L}\d_]{3,30}$/u', $name)) {
            $err['username'] = 'Логин — 3–30 символов: буквы, цифры, подчёркивание.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
            $err['email'] = 'Введите корректный email.';
        }
        if ($m = Validator::password($pass, (string)($_POST['password2'] ?? ''))) {
            $err['password'] = $m;
        }
        if (!$err && User::exists($email, $name)) {
            $err['email'] = 'Пользователь с таким email или логином уже есть.';
        }
        if ($err) {
            View::render('auth/register', ['old' => ['username' => $name, 'email' => $email], 'errors' => $err]);
            return;
        }
        Auth::login(User::create($name, $email, $pass));
        flash('Добро пожаловать, ' . $name . '!');
        redirect('/');
    }

    public function loginForm(): void
    {
        View::render('auth/login', ['old' => [], 'error' => null]);
    }

    public function login(): void
    {
        $email = trim((string)($_POST['email'] ?? ''));
        $now = time();
        if (($_SESSION['lock_until'] ?? 0) > $now) {
            View::render('auth/login', ['old' => ['email' => $email], 'error' => 'Слишком много попыток. Подождите минуту.']);
            return;
        }
        $user = User::findByEmail($email);
        if ($user && password_verify((string)($_POST['password'] ?? ''), $user['password_hash'])) {
            unset($_SESSION['fails'], $_SESSION['lock_until']);
            Auth::login((int)$user['id']);
            flash('Вы вошли как ' . $user['username'] . '.');
            redirect($user['role'] === 'admin' ? '/admin' : '/');
        }
        $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1;
        if ($_SESSION['fails'] >= 5) {
            $_SESSION['lock_until'] = $now + 60;
            $_SESSION['fails'] = 0;
        }
        View::render('auth/login', ['old' => ['email' => $email], 'error' => 'Неверный email или пароль.']);
    }

    public function logout(): void
    {
        Auth::logout();
        session_start();
        flash('Вы вышли из аккаунта.', 'info');
        redirect('/');
    }

    public function forgotForm(): void
    {
        View::render('auth/forgot', ['link' => null, 'sent' => false]);
    }

    /** Создаёт токен сброса. Ответ одинаков для любого email (не раскрываем, кто зарегистрирован). */
    public function forgot(): void
    {
        $cfg = require __DIR__ . '/../../config/config.php';
        $user = User::findByEmail(trim((string)($_POST['email'] ?? '')));
        $link = null;
        if ($user) {
            $link = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/reset/' . User::createResetToken((int)$user['id']);
            // Вместо реальной отправки письма пишем его в лог; на рабочем сервере здесь вызывается mail()/SMTP.
            file_put_contents(__DIR__ . '/../../storage/mail.log', date('c') . " To: {$user['email']}\nСсылка для сброса пароля: $link\n\n", FILE_APPEND);
        }
        View::render('auth/forgot', ['link' => $cfg['debug_reset_link'] ? $link : null, 'sent' => true]);
    }

    public function resetForm(string $token): void
    {
        if (!User::userIdByResetToken($token)) {
            flash('Ссылка недействительна или устарела.', 'danger');
            redirect('/forgot');
        }
        View::render('auth/reset', ['token' => $token, 'error' => null]);
    }

    public function reset(string $token): void
    {
        $uid = User::userIdByResetToken($token);
        if (!$uid) {
            flash('Ссылка недействительна или устарела.', 'danger');
            redirect('/forgot');
        }
        $err = Validator::password((string)($_POST['password'] ?? ''), (string)($_POST['password2'] ?? ''));
        if ($err) {
            View::render('auth/reset', ['token' => $token, 'error' => $err]);
            return;
        }
        User::setPassword($uid, $_POST['password']);
        User::deleteResetTokens($uid);
        flash('Пароль изменён. Войдите с новым паролем.');
        redirect('/login');
    }
}
