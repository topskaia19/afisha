<?php use App\Core\Csrf; ?>
<div class="auth-box">
  <h1 class="h3 mb-3">Восстановление пароля</h1>
  <?php if ($sent): ?>
    <div class="alert alert-info">Если такой email зарегистрирован, мы отправили ссылку для сброса пароля. Она действует 1 час.</div>
    <?php if ($link): ?><p class="small">Учебный режим (письмо не отправляется): <a href="<?= e($link) ?>">ссылка для сброса</a></p><?php endif; ?>
  <?php endif; ?>
  <form method="post" action="/forgot">
    <?= Csrf::field() ?>
    <div class="mb-3"><label class="form-label" for="email">Email</label><input id="email" name="email" type="email" class="form-control" required></div>
    <button class="btn btn-primary w-100">Отправить ссылку</button>
  </form>
</div>
