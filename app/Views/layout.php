<?php use App\Core\{Auth, Csrf}; ?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Афиша</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md site-nav">
  <div class="container">
    <a class="navbar-brand brand" href="/">Афиша</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Меню"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/events">Все события</a></li>
        <li class="nav-item"><a class="nav-link" href="/events/create">Добавить событие</a></li>
        <?php if (Auth::isAdmin()): ?><li class="nav-item"><a class="nav-link" href="/admin">Админ-панель</a></li><?php endif; ?>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if (Auth::check()): ?>
          <span class="small text-muted"><?= e(Auth::user()['username']) ?></span>
          <form method="post" action="/logout"><?= Csrf::field() ?><button class="btn btn-sm btn-outline-dark">Выйти</button></form>
        <?php else: ?>
          <a class="btn btn-sm btn-outline-dark" href="/login">Войти</a>
          <a class="btn btn-sm btn-dark" href="/register">Регистрация</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="container py-4">
  <?php foreach (flash() as [$type, $msg]): ?>
    <div class="alert alert-<?= e($type) ?>" role="alert"><?= e($msg) ?></div>
  <?php endforeach; ?>
  <?= $content ?>
</main>
<footer class="container py-4 small text-muted">Афиша — учебный проект.</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>
