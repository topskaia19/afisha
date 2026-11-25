<h1 class="h3 mb-3">Все события</h1>
<form method="get" action="/events" class="filters row g-2 mb-4">
  <div class="col-md-4"><label class="form-label" for="q">Поиск</label>
    <input id="q" class="form-control" name="q" maxlength="100" value="<?= e($f['q'] ?? '') ?>"></div>
  <div class="col-md-4"><label class="form-label" for="category">Категория</label>
    <select id="category" class="form-select" name="category">
      <option value="">Любая</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= ($f['category'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select></div>
  <div class="col-md-4"><label class="form-label" for="format">Формат</label>
    <select id="format" class="form-select" name="format">
      <option value="">Любой</option>
      <option value="offline" <?= ($f['format'] ?? '') === 'offline' ? 'selected' : '' ?>>Офлайн</option>
      <option value="online" <?= ($f['format'] ?? '') === 'online' ? 'selected' : '' ?>>Онлайн</option>
    </select></div>
  <div class="col-md-3"><label class="form-label" for="date_from">Дата от</label>
    <input id="date_from" type="date" class="form-control" name="date_from" value="<?= e($f['date_from'] ?? '') ?>"></div>
  <div class="col-md-3"><label class="form-label" for="date_to">Дата до</label>
    <input id="date_to" type="date" class="form-control" name="date_to" value="<?= e($f['date_to'] ?? '') ?>"></div>
  <div class="col-md-3"><label class="form-label" for="price_max">Цена до, MDL</label>
    <input id="price_max" type="number" min="0" class="form-control" name="price_max" value="<?= e($f['price_max'] ?? '') ?>"></div>
  <div class="col-md-3 d-flex align-items-end gap-3">
    <div class="form-check"><input class="form-check-input" type="checkbox" id="free" name="free" value="1" <?= !empty($f['free']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="free">Бесплатные</label></div>
    <button class="btn btn-primary ms-auto">Найти</button>
  </div>
</form>

<?php if (!$events): ?>
  <p class="text-muted">Ничего не найдено. Попробуйте изменить условия поиска или <a href="/events">сбросить фильтры</a>.</p>
<?php else: ?>
  <p class="text-muted">Найдено: <?= count($events) ?></p>
  <div class="row g-3">
    <?php foreach ($events as $e): ?><div class="col-md-6"><?php require __DIR__ . '/../_event_card.php'; ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>
