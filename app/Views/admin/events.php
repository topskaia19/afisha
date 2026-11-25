<?php use App\Core\Csrf; require __DIR__ . '/_nav.php'; ?>
<h1 class="h3 mb-3">События</h1>
<div class="table-responsive"><table class="table align-middle">
  <thead><tr><th>ID</th><th>Название</th><th>Дата</th><th>Категория</th><th>Автор</th><th>Статус</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($events as $e): ?>
    <tr>
      <td><?= (int)$e['id'] ?></td>
      <td><a href="/events/<?= (int)$e['id'] ?>"><?= e($e['title']) ?></a></td>
      <td><?= e(fmt_date($e['event_date'])) ?></td><td><?= e($e['category']) ?></td><td><?= e($e['author']) ?></td>
      <td><span class="badge text-bg-<?= $e['status'] === 'published' ? 'success' : 'warning' ?>"><?= $e['status'] === 'published' ? 'Опубликовано' : 'На модерации' ?></span></td>
      <td class="text-end text-nowrap">
        <?php if ($e['status'] === 'pending'): ?>
          <form method="post" action="/admin/events/<?= (int)$e['id'] ?>/approve" class="d-inline"><?= Csrf::field() ?><button class="btn btn-sm btn-success">Опубликовать</button></form>
        <?php endif; ?>
        <a class="btn btn-sm btn-outline-dark" href="/admin/events/<?= (int)$e['id'] ?>/edit">Изменить</a>
        <form method="post" action="/admin/events/<?= (int)$e['id'] ?>/delete" class="d-inline" data-confirm="Удалить событие?"><?= Csrf::field() ?><button class="btn btn-sm btn-outline-danger">Удалить</button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
