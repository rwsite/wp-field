# WP_Field v2.0 — Universal HTML Field Generator for WordPress

Минималистичная, расширяемая библиотека для создания полей в WordPress с поддержкой:
- 20+ типов полей (базовые, выборные, продвинутые, композитные)
- Системы зависимостей между полями
- Всех типов хранилищ (post meta, options, term meta, user meta, comment meta)
- Встроенных WP компонентов (wp_editor, wp-color-picker, wp.media)
- Без внешних зависимостей (JS/CSS)

## Быстрый старт

```php
// Простое текстовое поле
WP_Field::make([
    'id'    => 'shop_name',
    'type'  => 'text',
    'label' => 'Название магазина',
]);

// Select с зависимостью
WP_Field::make([
    'id'      => 'delivery_type',
    'type'    => 'select',
    'label'   => 'Тип доставки',
    'options' => ['courier' => 'Курьер', 'pickup' => 'Самовывоз'],
]);

WP_Field::make([
    'id'    => 'delivery_address',
    'type'  => 'text',
    'label' => 'Адрес доставки',
    'dependency' => [
        ['delivery_type', '==', 'courier'],
    ],
]);
```

## Использование

### Базовый синтаксис

```php
WP_Field::make(array $field_config, bool $output = true, string $storage_type = 'post', int|string $storage_id = null);
```

### Параметры поля

```php
[
    'id'                 => 'field_id',              // Обязательно
    'type'               => 'text',                  // Обязательно
    'label'              => 'Название',              // Обязательно
    'name'               => 'custom_name',           // Переопределить name атрибут
    'value'              => 'explicit_value',        // Явное значение
    'default'            => 'default_value',         // Значение по умолчанию
    'placeholder'        => 'Введите...',             // Placeholder
    'class'              => 'regular-text',          // CSS класс
    'desc'               => 'Описание поля',         // Описание
    'readonly'           => false,                   // Только для чтения
    'disabled'           => false,                   // Отключено
    'custom_attributes'  => ['data-x' => 'y'],       // Кастомные атрибуты
    'dependency'         => [                        // Условия видимости
        ['field_id', '==', 'value'],
        'relation' => 'AND',
    ],
]
```

### Типы хранилищ

```php
// Post meta (по умолчанию)
WP_Field::make($field, true, 'post', $post_id);

// Option
WP_Field::make($field, true, 'options');

// Term meta
WP_Field::make($field, true, 'term', $term_id);

// User meta
WP_Field::make($field, true, 'user', $user_id);

// Comment meta
WP_Field::make($field, true, 'comment', $comment_id);
```

## Поддерживаемые типы полей

### Базовые (Basic)
- `text` — текстовое поле
- `password` — пароль
- `email` — email
- `url` — URL
- `tel` — телефон
- `number` — число
- `range` — диапазон
- `hidden` — скрытое поле
- `textarea` — многострочный текст

### Выборные (Choice)
- `select` — выпадающий список
- `multiselect` — множественный выбор
- `radio` — радиокнопки
- `checkbox` — одиночный чекбокс
- `checkbox_group` — группа чекбоксов

### Продвинутые (Advanced)
- `editor` — wp_editor
- `media` — медиа (ID или URL)
- `image` — изображение с preview
- `file` — файл
- `gallery` — галерея
- `color` — color picker
- `date` — дата
- `time` — время
- `datetime` — дата и время

### Композитные (Composite)
- `group` — вложенные поля
- `repeater` — повторяющиеся элементы

## Примеры

### Зависимости (Dependency)

```php
// Показать поле только если другое поле имеет значение
WP_Field::make([
    'id'    => 'courier_address',
    'type'  => 'text',
    'label' => 'Адрес доставки',
    'dependency' => [
        ['delivery_type', '==', 'courier'],
    ],
]);

// Множественные условия (AND)
WP_Field::make([
    'id'    => 'special_field',
    'type'  => 'text',
    'label' => 'Специальное поле',
    'dependency' => [
        ['field1', '==', 'value1'],
        ['field2', '!=', 'value2'],
        'relation' => 'AND',
    ],
]);

// Множественные условия (OR)
WP_Field::make([
    'id'    => 'notification',
    'type'  => 'text',
    'label' => 'Уведомление',
    'dependency' => [
        ['type', '==', 'sms'],
        ['type', '==', 'email'],
        'relation' => 'OR',
    ],
]);
```

### Repeater

```php
WP_Field::make([
    'id'       => 'work_times',
    'type'     => 'repeater',
    'label'    => 'Время работы',
    'min'      => 1,
    'max'      => 7,
    'add_text' => 'Добавить день',
    'fields'   => [
        [
            'id'      => 'day',
            'type'    => 'select',
            'label'   => 'День',
            'options' => ['mon' => 'Пн', 'tue' => 'Вт'],
        ],
        [
            'id'    => 'from',
            'type'  => 'time',
            'label' => 'С',
        ],
        [
            'id'    => 'to',
            'type'  => 'time',
            'label' => 'По',
        ],
    ],
]);
```

### Group

```php
WP_Field::make([
    'id'    => 'address',
    'type'  => 'group',
    'label' => 'Адрес',
    'fields' => [
        ['id' => 'city', 'type' => 'text', 'label' => 'Город'],
        ['id' => 'street', 'type' => 'text', 'label' => 'Улица'],
        ['id' => 'number', 'type' => 'text', 'label' => 'Номер'],
    ],
]);
```

## Операторы зависимостей

- `==` — равно
- `!=` — не равно
- `>`, `>=`, `<`, `<=` — сравнение
- `in` — в массиве
- `not_in` — не в массиве
- `contains` — содержит
- `not_contains` — не содержит
- `empty` — пусто
- `not_empty` — не пусто

## Документация

- **QUICKSTART.md** — быстрый старт с примерами
- **WP_FIELD_PLAN.md** — архитектура и план развития
- **docs/** — дополнительная документация

## Совместимость

- WordPress 4.6+
- PHP 7.4+
- Без внешних зависимостей (использует встроенные WP скрипты)

