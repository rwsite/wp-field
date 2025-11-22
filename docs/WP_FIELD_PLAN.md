# План улучшения WP_Field (минималистичный, один класс + assets)

## Цели
- Покрыть все типы полей, используемые в WordPress-админке (basic, choices, advanced, composite).
- Добавить систему зависимостей (condition/dependency), как в Codestar, но проще.
- Сохранить API `WP_Field::make()` и алиасы; минимальные правки текущего кода.
- Вся логика в одном классе `WP_Field`. Шаблоны/JS/CSS — в `assets/` для простоты.

## Архитектура
- Единый класс `WP_Field` со следующими блоками:
  - Регистр типов: `type => [renderer, defaults, sanitize_cb, validate_cb]`.
  - Общий враппер: label, description, help, error, wrapper классы, `data-*` для зависимостей.
  - Резолвер значений: `options|post|term|user|comment` + `default` + `value` с приоритетом.
  - Генератор атрибутов: `name`, `id`, `class`, `custom_attributes`, `aria-*`.
  - Зависимости: серверная первичная оценка + фронтовый toggler по `data-dep`.
  - Enqueue: одноразовая инициализация assets (static-флаг, `admin_enqueue_scripts`).
  - Алиасы: `title|label`, `value|val`, `custom_attributes|attributes|attr|atts` — сохраняем.
  - Расширяемость: фильтры `apply_filters('wp_field_render', $html, $field)` и `do_action('wp_field_after_render', $field)`.

## Поддерживаемые типы (покрытие)
- Базовые: `text`, `password`, `email`, `url`, `tel`, `number`, `range`, `hidden`, `textarea`.
- Выбор: `select` (single), `multiselect` (native), `radio`, `checkbox` (single), `checkbox_group` (multiple).
- Продвинутые:
  - `editor` (wp_editor)
  - Медиа: `media` (id/url), `image`, `file`, `gallery`
  - Цвет: `color` (wp-color-picker)
  - Даты/время: `date`, `time`, `datetime` (native input; опц. jQuery UI fallback)
- Композитные: `group` (вложенные поля), `repeater` (массив элементов на базе под-полей).

## Схема поля (минимум)
```php
$field = [
  'id'    => 'sample_id',            // meta-key | option name
  'type'  => 'text',                 // см. список типов
  'label' => 'Title',                // алиас: title

  // опционально
  'name'   => 'custom_name',         // если нужно переопределить name
  'class'  => 'regular-text',
  'value'  => null,                  // алиас: val
  'default'=> null,
  'desc'   => '',
  'options'=> [ 'k' => 'Label' ],    // для select/radio/checkbox_group
  'parse_options' => false,          // поддержка text->options
  'custom_attributes' => [ 'data-x' => 'y' ], // алиасы: attributes|attr|atts

  // advanced
  'sanitize_cb' => null,
  'validate_cb' => null,
  'placeholder' => '',
  'readonly'    => false,
  'disabled'    => false,

  // dependency
  'dependency' => [
    // Пример: показывать, если enable == yes И type in [a,b]
    [ 'enable', '==', 'yes' ],
    [ 'type', 'in', ['a','b'] ],
    'relation' => 'AND', // по умолчанию AND
  ],

  // group/repeater
  'fields' => [ /* под-поля */ ],
  'max'    => 0,   // ограничение для repeater (0 = без ограничений)
  'min'    => 0,
];
```

Поддерживаемые операторы зависимостей: `==`, `!=`, `>`, `>=`, `<`, `<=`, `in`, `not_in`, `contains`, `not_contains`, `empty`, `not_empty`.

## Враппер и разметка
- Общий контейнер: `<div class="wp-field" data-type="..." data-id="..." data-dep='...json...'>`.
- Внутри: label (с `for`), control, desc (`.description`).
- Классы состояния: `.is-hidden` (по dependency), `.has-error`.
- A11y: `aria-describedby` для desc, корректный `id`/`for`.

## Система зависимостей
- PHP: первичная оценка (для стартового состояния) — добавляем класс `.is-hidden`.
- JS (assets/js/wp-field.js):
  - Слушаем события `change input/select/textarea` по id/имени целевых полей.
  - Пересчет условий (AND/OR, вложенность массивов поддерживается минимально).
  - Тогглинг `.is-hidden` + `aria-hidden`.
  - Скоуп для group/repeater (ищем значения внутри ближайшего контейнера).

## Assets (минимум, без тяжёлых зависимостей)
- `assets/js/wp-field.js` — инициализация:
  - dependency toggler
  - color picker (`wp-color-picker`)
  - media frame (wp.media) для image/file/gallery
  - date/time нативно; хук для опц. jQuery UI при необходимости
  - repeater: add/remove, клоны по шаблону
- `assets/css/wp-field.css` — базовые стили/состояния
- `assets/templates/` — `repeater-item.html`, `group.html` (минимальные HTML-шаблоны)
- Enqueue: однократная инициализация при первом рендере (`static $enqueued = false`).

## Совместимость
- Сохраняем текущий `make($params, $output = true)`.
- Алиасы полей остаются.
- Типы, которые уже есть (checkbox, number, editor, media, radio, select, textarea, datetime, image_picker, input) — поддерживаются без изменений, добавляем новые.
- Миграция: старые ключи продолжают работать; новые — опциональны.

## Валидация и санитизация
- На уровне поля: `sanitize_cb` и `validate_cb` (callable). По умолчанию:
  - `text|email|url|tel` — `sanitize_text_field`
  - `number|range` — `(int|float)` + `min/max/step`
  - `color` — `sanitize_hex_color`
  - `editor` — `wp_kses_post`
  - media ids/urls — числовое/id + esc_url

## Примеры использования
```php
// Text с зависимостью
WP_Field::make([
  'id' => 'shop_name',
  'type' => 'text',
  'label' => __('Shop Name', 'wp-field'),
  'dependency' => [ ['enable_shop', '==', 'yes'] ],
]);

// Select + checkbox_group
WP_Field::make([
  'id' => 'type', 'type' => 'select', 'label' => 'Type',
  'options' => ['a' => 'Type A', 'b' => 'Type B'],
]);
WP_Field::make([
  'id' => 'features', 'type' => 'checkbox_group', 'label' => 'Features',
  'options' => ['x' => 'X', 'y' => 'Y'],
  'dependency' => [ ['type', '==', 'a'] ],
]);

// Media image
WP_Field::make([
  'id' => 'logo', 'type' => 'image', 'label' => 'Logo',
]);

// Group
WP_Field::make([
  'id' => 'address', 'type' => 'group', 'label' => 'Address',
  'fields' => [
    ['id' => 'city', 'type' => 'text', 'label' => 'City'],
    ['id' => 'street', 'type' => 'text', 'label' => 'Street'],
  ],
]);

// Repeater
WP_Field::make([
  'id' => 'work_times', 'type' => 'repeater', 'label' => 'Work Times',
  'min' => 0, 'max' => 7,
  'fields' => [
    ['id' => 'day', 'type' => 'select', 'label' => 'Day', 'options' => [ 'mon' => 'Mon', 'tue' => 'Tue' ]],
    ['id' => 'from', 'type' => 'time', 'label' => 'From'],
    ['id' => 'to', 'type' => 'time', 'label' => 'To'],
  ],
]);
```

## Хуки интеграции
- `admin_enqueue_scripts` — регистрация/подключение `assets/js/wp-field.js`, `assets/css/wp-field.css` (только в админке, однократно).
- `current_screen` — можно сузить подключение по страницам (опционально).

## Критерии готовности
- [ ] Реестр типов и дефолтов
- [ ] Общий враппер + атрибуты + i18n + a11y
- [ ] Базовые поля
- [ ] Поля выбора
- [ ] Продвинутые поля
- [ ] Group
- [ ] Repeater (+ шаблоны)
- [ ] Dependency (PHP + JS)
- [ ] Assets (JS/CSS) + enqueue
- [ ] Документация quickstart в `docs/`
- [ ] Примеры конфигов

## Риски/ограничения
- Нативные `date/time/datetime` могут отличаться по браузерам. Решение: оставить нативные, добавить хук и класс для опционального подключения jQuery UI.
- `wp_editor` дорогой по ресурсам — инициализировать только при наличии типа `editor`.
- Для `repeater` важно правильно строить `name` (массивы): `name="address[0][city]"` и т. п.

## Пошаговый чеклист (исполнение)
- [ ] Реестр типов + дефолты + алиасы
- [ ] Общий рендерер-обёртка
- [ ] Базовые инпуты
- [ ] Select/Multiselect/Radio/Checkbox/Checkbox_group
- [ ] Editor/Media/Color/Date/Time/Datetime
- [ ] Group
- [ ] Repeater (+ templates)
- [ ] Dependency PHP + JS
- [ ] Assets js/css + enqueue
- [ ] Документация + примеры
