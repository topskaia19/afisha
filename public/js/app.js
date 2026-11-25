/**
 * Клиентская валидация формы события и подтверждение опасных действий.
 * Серверная валидация (Validator::event) выполняется всегда — клиентская лишь ускоряет обратную связь.
 */
(function () {
  'use strict';

  // Подтверждение удаления: <form data-confirm="Текст?">
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (ev) {
      if (!window.confirm(f.dataset.confirm)) { ev.preventDefault(); }
    });
  });

  var form = document.getElementById('event-form');
  if (!form) { return; }

  var cityGroup = document.getElementById('city-group');
  var city = document.getElementById('city');
  var date = document.getElementById('event-date') || document.getElementById('event_date');

  /** Показывает поле «Город» только для офлайн-событий. */
  function syncFormat() {
    var offline = form.querySelector('input[name="format"]:checked').value === 'offline';
    cityGroup.hidden = !offline;
    city.required = offline;
  }
  form.querySelectorAll('input[name="format"]').forEach(function (r) { r.addEventListener('change', syncFormat); });
  syncFormat();

  /** Выводит сообщение под полем. */
  function setError(input, message) {
    var box = input.parentElement.querySelector('.js-error');
    if (!box) {
      box = document.createElement('div');
      box.className = 'invalid-feedback d-block js-error';
      input.parentElement.appendChild(box);
    }
    box.textContent = message;
    input.classList.toggle('is-invalid', message !== '');
  }

  form.addEventListener('submit', function (ev) {
    var ok = true;
    var rules = [
      ['title', function (v) { return v.length >= 3 && v.length <= 100 ? '' : 'Название — от 3 до 100 символов.'; }],
      ['description', function (v) { return v.length >= 10 && v.length <= 2000 ? '' : 'Описание — от 10 до 2000 символов.'; }],
      ['category_id', function (v) { return v ? '' : 'Выберите категорию из списка.'; }],
      ['event_date', function (v) {
        if (!v) { return 'Укажите дату и время события.'; }
        // На странице редактирования прошедшие даты допустимы (форма помечена data-allow-past)
        return form.dataset.allowPast || new Date(v) > new Date() ? '' : 'Дата должна быть в будущем.';
      }],
      ['price', function (v) { return v !== '' && Number(v) >= 0 ? '' : 'Цена — число не меньше 0.'; }],
      ['city', function (v) { return city.required && v.trim() === '' ? 'Для офлайн-события укажите город.' : ''; }]
    ];
    rules.forEach(function (r) {
      var input = form.elements[r[0]];
      var msg = r[1](input.value.trim());
      setError(input, msg);
      if (msg) { ok = false; }
    });
    if (!ok) { ev.preventDefault(); }
  });
})();
