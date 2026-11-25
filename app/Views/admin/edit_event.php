<?php require __DIR__ . '/_nav.php'; ?>
<h1 class="h3 mb-3">Редактирование события №<?= (int)$id ?></h1>
<?php $allowPast = true; $action = '/admin/events/' . (int)$id . '/edit'; $submit = 'Сохранить изменения'; require __DIR__ . '/../events/_form.php'; ?>
