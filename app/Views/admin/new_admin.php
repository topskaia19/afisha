<?php use App\Core\Csrf; require __DIR__ . '/_nav.php';
$err = fn(string $k) => isset($errors[$k]) ? '<div class="invalid-feedback d-block">' . e($errors[$k]) . '</div>' : ''; ?>
<div class="auth-box ms-0">
  <h1 class="h3 mb-3">Новый администратор</h1>
  <form method="post" action="/admin/admins/new" novalidate>
    <?= Csrf::field() ?>
    <div class="mb-3"><label class="form-label" for="username">Логин</label>
      <input id="username" name="username" class="form-control<?= isset($errors['username']) ? ' is-invalid' : '' ?>" required value="<?= e($old['username'] ?? '') ?>"><?= $err('username') ?></div>
    <div class="mb-3"><label class="form-label" for="email">Email</label>
      <input id="email" name="email" type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" required value="<?= e($old['email'] ?? '') ?>"><?= $err('email') ?></div>
    <div class="mb-3"><label class="form-label" for="password">Пароль</label>
      <input id="password" name="password" type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" required><?= $err('password') ?></div>
    <div class="mb-3"><label class="form-label" for="password2">Повторите пароль</label>
      <input id="password2" name="password2" type="password" class="form-control" required></div>
    <button class="btn btn-primary">Создать администратора</button>
  </form>
</div>
