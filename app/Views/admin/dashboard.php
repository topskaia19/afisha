<?php require __DIR__ . '/_nav.php'; ?>
<h1 class="h3 mb-3">Админ-панель</h1>
<dl class="stats">
  <div><dt>Опубликовано</dt><dd><?= $stats['events'] ?></dd></div>
  <div><dt>На модерации</dt><dd><?= $stats['pending'] ?></dd></div>
  <div><dt>Пользователей</dt><dd><?= (int)$users ?></dd></div>
  <div><dt>Категорий</dt><dd><?= (int)$categories ?></dd></div>
</dl>
<?php if ($stats['pending']): ?><a class="btn btn-primary mt-3" href="/admin/events">Проверить новые события</a><?php endif; ?>
