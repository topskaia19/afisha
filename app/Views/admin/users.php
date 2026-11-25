<?php use App\Core\{Auth, Csrf}; require __DIR__ . '/_nav.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">Пользователи</h1>
  <a class="btn btn-dark" href="/admin/admins/new">Создать администратора</a>
</div>
<div class="table-responsive"><table class="table align-middle">
  <thead><tr><th>ID</th><th>Логин</th><th>Email</th><th>Роль</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($users as $u): $self = $u['id'] == Auth::user()['id']; ?>
    <tr>
      <td><?= (int)$u['id'] ?></td><td><?= e($u['username']) ?></td><td><?= e($u['email']) ?></td>
      <td>
        <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/role" class="d-flex gap-2">
          <?= Csrf::field() ?>
          <select name="role" class="form-select form-select-sm" <?= $self ? 'disabled' : '' ?>>
            <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
          </select>
          <button class="btn btn-sm btn-outline-dark" <?= $self ? 'disabled' : '' ?>>Сохранить</button>
        </form>
      </td>
      <td class="text-end">
        <?php if (!$self): ?>
        <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/delete" data-confirm="Удалить пользователя и все его события?">
          <?= Csrf::field() ?><button class="btn btn-sm btn-outline-danger">Удалить</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
