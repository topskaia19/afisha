<section class="hero mb-5">
  <h1 class="display-title">Что происходит<br>в городе</h1>
  <p class="lead">Концерты, спектакли, выставки и митапы. Найдите событие или добавьте своё.</p>
  <form action="/events" method="get" class="d-flex gap-2 hero-search">
    <input class="form-control form-control-lg" name="q" placeholder="Название или тема" aria-label="Поиск">
    <button class="btn btn-primary btn-lg">Найти</button>
  </form>
</section>

<section class="mb-5">
  <h2 class="h4 mb-3">Ближайшие события</h2>
  <?php if (!$events): ?>
    <p class="text-muted">Пока нет предстоящих событий. <a href="/events/create">Добавьте первое</a>.</p>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($events as $e): ?><div class="col-md-6"><?php require __DIR__ . '/../_event_card.php'; ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<div class="row g-4">
  <section class="col-md-7">
    <h2 class="h4 mb-3">Категории</h2>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach ($categories as $c): ?>
        <a class="chip" href="/events?category=<?= (int)$c['id'] ?>"><?= e($c['name']) ?> <b><?= (int)$c['cnt'] ?></b></a>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="col-md-5">
    <h2 class="h4 mb-3">В афише сейчас</h2>
    <dl class="stats">
      <div><dt>Событий</dt><dd><?= $stats['events'] ?></dd></div>
      <div><dt>Бесплатных</dt><dd><?= $stats['free'] ?></dd></div>
      <div><dt>Онлайн</dt><dd><?= $stats['online'] ?></dd></div>
    </dl>
  </section>
</div>
