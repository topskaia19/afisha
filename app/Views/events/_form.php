<?php
use App\Core\Csrf;
$v = fn(string $k, $d = '') => $old[$k] ?? $d;
$cls = fn(string $k) => isset($errors[$k]) ? ' is-invalid' : '';
$err = fn(string $k) => isset($errors[$k]) ? '<div class="invalid-feedback d-block">' . e($errors[$k]) . '</div>' : '';
?>
<form method="post" action="<?= e($action) ?>" class="event-form" id="event-form" <?= !empty($allowPast) ? 'data-allow-past="1"' : '' ?> novalidate>
  <?= Csrf::field() ?>
  <div class="mb-3">
    <label class="form-label" for="title">Название</label>
    <input id="title" name="title" type="text" class="form-control<?= $cls('title') ?>" minlength="3" maxlength="100" required value="<?= e($v('title')) ?>">
    <?= $err('title') ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="description">Описание</label>
    <textarea id="description" name="description" rows="5" class="form-control<?= $cls('description') ?>" minlength="10" maxlength="2000" required><?= e($v('description')) ?></textarea>
    <?= $err('description') ?>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label" for="category_id">Категория</label>
      <select id="category_id" name="category_id" class="form-select<?= $cls('category_id') ?>" required>
        <option value="">Выберите…</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= $v('category_id') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?= $err('category_id') ?>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label" for="event_date">Дата и время</label>
      <input id="event_date" name="event_date" type="datetime-local" class="form-control<?= $cls('event_date') ?>" required value="<?= e($v('event_date')) ?>">
      <?= $err('event_date') ?>
    </div>
  </div>
  <div class="mb-3">
    <span class="form-label d-block">Формат</span>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="format" id="f_off" value="offline" <?= $v('format', 'offline') === 'offline' ? 'checked' : '' ?>><label class="form-check-label" for="f_off">Офлайн</label></div>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="format" id="f_on" value="online" <?= $v('format') === 'online' ? 'checked' : '' ?>><label class="form-check-label" for="f_on">Онлайн</label></div>
    <?= $err('format') ?>
  </div>
  <div class="mb-3" id="city-group">
    <label class="form-label" for="city">Город</label>
    <input id="city" name="city" type="text" class="form-control<?= $cls('city') ?>" maxlength="60" value="<?= e($v('city')) ?>">
    <?= $err('city') ?>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label" for="price">Цена, MDL (0 — бесплатно)</label>
      <input id="price" name="price" type="number" min="0" max="100000" step="1" class="form-control<?= $cls('price') ?>" required value="<?= e($v('price', 0)) ?>">
      <?= $err('price') ?>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label" for="age_limit">Возраст</label>
      <select id="age_limit" name="age_limit" class="form-select<?= $cls('age_limit') ?>">
        <?php foreach ([0 => 'Без ограничений', 6 => '6+', 12 => '12+', 16 => '16+', 18 => '18+'] as $k => $label): ?>
          <option value="<?= $k ?>" <?= (int)$v('age_limit') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
      <?= $err('age_limit') ?>
    </div>
  </div>
  <button class="btn btn-primary"><?= e($submit) ?></button>
</form>
