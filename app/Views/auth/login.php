<?php use App\Core\Csrf; ?>
<div class="auth-box">
  <h1 class="h3 mb-3">Вход</h1>
  <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="/login">
    <?= Csrf::field() ?>
    <div class="mb-3"><label class="form-label" for="email">Email</label>
      <input id="email" name="email" type="email" class="form-control" required value="<?= e($old['email'] ?? '') ?>"></div>
    <div class="mb-3"><label class="form-label" for="password">Пароль</label>
      <input id="password" name="password" type="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Войти</button>
  </form>
  <p class="mt-3 small"><a href="/forgot">Забыли пароль?</a> · <a href="/register">Создать аккаунт</a></p>
</div>
