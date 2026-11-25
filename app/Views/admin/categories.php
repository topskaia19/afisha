<?php use App\Core\Csrf; require __DIR__ . '/_nav.php'; ?>
<h1 class="h3 mb-3">Категории</h1>
<form method="post" action="/admin/categories" class="d-flex gap-2 mb-4" style="max-width:420px">
  <?= Csrf::field() ?>
  <input name="name" class="form-control" placeholder="Новая категория" minlength="2" maxlength="40" required aria-label="Название категории">
  <button class="btn btn-dark">Добавить</button>
</form>
<table class="table align-middle" style="max-width:560px"><tbody>
<?php foreach ($categories as $c): ?>
  <tr><td><?= e($c['name']) ?></td><td class="text-muted"><?= (int)$c['cnt'] ?> соб.</td>
    <td class="text-end"><form method="post" action="/admin/categories/<?= (int)$c['id'] ?>/delete" data-confirm="Удалить категорию?"><?= Csrf::field() ?><button class="btn btn-sm btn-outline-danger">Удалить</button></form></td></tr>
<?php endforeach; ?>
</tbody></table>
