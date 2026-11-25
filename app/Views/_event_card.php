<?php $t = strtotime($e['event_date']); ?>
<a class="ticket" href="/events/<?= (int)$e['id'] ?>">
  <div class="ticket-date">
    <span class="d"><?= date('j', $t) ?></span>
    <span class="m"><?= e(mb_substr(explode(' ', fmt_date($e['event_date']))[1], 0, 3)) ?></span>
  </div>
  <div class="ticket-body">
    <div class="small text-muted"><?= e($e['category']) ?> · <?= $e['format'] === 'online' ? 'Онлайн' : e($e['city']) ?> · <?= date('H:i', $t) ?></div>
    <h3 class="ticket-title"><?= e($e['title']) ?></h3>
    <div class="ticket-price"><?= e(fmt_price($e['price'])) ?><?= $e['age_limit'] ? ' · ' . (int)$e['age_limit'] . '+' : '' ?></div>
  </div>
</a>
