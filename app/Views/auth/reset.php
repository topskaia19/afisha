<?php use App\Core\Csrf; ?>
<div class="auth-box">
  <h1 class="h3 mb-3">Новый пароль</h1>
  <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="/reset/<?= e($token) ?>">
    <?= Csrf::field() ?>
    <div class="mb-3"><label class="form-label" for="password">Новый пароль</label><input id="password" name="password" type="password" class="form-control" required minlength="8"></div>
    <div class="mb-3"><label class="form-label" for="password2">Повторите пароль</label><input id="password2" name="password2" type="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Сохранить пароль</button>
  </form>
</div>
