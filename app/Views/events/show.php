<a href="/events" class="small">← Все события</a>
<article class="mt-3 event-page">
  <?php if ($e['status'] === 'pending'): ?><div class="alert alert-warning">Событие на модерации и пока не видно другим пользователям.</div><?php endif; ?>
  <p class="text-muted mb-1"><?= e($e['category']) ?></p>
  <h1 class="display-title h2"><?= e($e['title']) ?></h1>
  <ul class="list-unstyled my-3">
    <li><b>Когда:</b> <?= e(fmt_date($e['event_date'])) ?></li>
    <li><b>Где:</b> <?= $e['format'] === 'online' ? 'Онлайн' : e($e['city']) ?></li>
    <li><b>Цена:</b> <?= e(fmt_price($e['price'])) ?></li>
    <li><b>Возраст:</b> <?= $e['age_limit'] ? (int)$e['age_limit'] . '+' : 'без ограничений' ?></li>
    <li><b>Организатор:</b> <?= e($e['author']) ?></li>
  </ul>
  <p class="event-desc"><?= nl2br(e($e['description'])) ?></p>
</article>
