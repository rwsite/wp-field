# WP_Field - Быстрый старт

## Установка

Класс `WP_Field` уже встроен в плагин. Просто используйте его:

```php
require_once plugin_dir_path(__FILE__) . 'lib/wp-field/WP_Field.php';
```

## Базовое использование

### Простое текстовое поле

```php
WP_Field::make([
    'id'    => 'shop_name',
    'type'  => 'text',
    'label' => 'Название магазина',
]);
```

### Получение значения

```php
$value = get_option('shop_name');
// или для post meta:
$value = get_post_meta(get_the_ID(), 'shop_name', true);
```

## Типы полей

### Базовые (text, password, email, url, tel, number, range, hidden, textarea)

```php
WP_Field::make([
    'id'          => 'email',
    'type'        => 'email',
    'label'       => 'Email',
    'placeholder' => 'user@example.com',
    'required'    => true,
]);

WP_Field::make([
    'id'    => 'age',
    'type'  => 'number',
    'label' => 'Возраст',
    'min'   => 0,
    'max'   => 120,
    'step'  => 1,
]);
```

### Select, Radio, Checkbox

```php
// Select
WP_Field::make([
    'id'      => 'country',
    'type'    => 'select',
    'label'   => 'Страна',
    'options' => [
        'ru' => 'Россия',
        'us' => 'США',
        'uk' => 'Великобритания',
    ],
]);

// Radio
WP_Field::make([
    'id'      => 'delivery_type',
    'type'    => 'radio',
    'label'   => 'Тип доставки',
    'options' => [
        'courier' => 'Курьер',
        'pickup'  => 'Самовывоз',
    ],
]);

// Checkbox (одиночный)
WP_Field::make([
    'id'    => 'subscribe',
    'type'  => 'checkbox',
    'label' => 'Подписаться на новости',
]);

// Checkbox group (множественный выбор)
WP_Field::make([
    'id'      => 'features',
    'type'    => 'checkbox_group',
    'label'   => 'Функции',
    'options' => [
        'sms'  => 'SMS уведомления',
        'push' => 'Push уведомления',
        'email' => 'Email уведомления',
    ],
]);
```

### Продвинутые поля

```php
// Editor (wp_editor)
WP_Field::make([
    'id'             => 'description',
    'type'           => 'editor',
    'label'          => 'Описание',
    'rows'           => 10,
    'media_buttons'  => true,
    'wpautop'        => true,
]);

// Image
WP_Field::make([
    'id'    => 'logo',
    'type'  => 'image',
    'label' => 'Логотип',
]);

// File
WP_Field::make([
    'id'    => 'document',
    'type'  => 'file',
    'label' => 'Документ',
]);

// Gallery
WP_Field::make([
    'id'    => 'gallery',
    'type'  => 'gallery',
    'label' => 'Галерея',
]);

// Color picker
WP_Field::make([
    'id'    => 'primary_color',
    'type'  => 'color',
    'label' => 'Основной цвет',
]);

// Date/Time/Datetime
WP_Field::make([
    'id'    => 'event_date',
    'type'  => 'date',
    'label' => 'Дата события',
]);

WP_Field::make([
    'id'    => 'event_time',
    'type'  => 'time',
    'label' => 'Время события',
]);

WP_Field::make([
    'id'    => 'event_datetime',
    'type'  => 'datetime',
    'label' => 'Дата и время события',
]);
```

### Композитные поля

```php
// Group (вложенные поля)
WP_Field::make([
    'id'    => 'address',
    'type'  => 'group',
    'label' => 'Адрес',
    'fields' => [
        [
            'id'    => 'city',
            'type'  => 'text',
            'label' => 'Город',
        ],
        [
            'id'    => 'street',
            'type'  => 'text',
            'label' => 'Улица',
        ],
        [
            'id'    => 'number',
            'type'  => 'text',
            'label' => 'Номер дома',
        ],
    ],
]);

// Repeater (массив элементов)
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
            'label'   => 'День недели',
            'options' => [
                'mon' => 'Понедельник',
                'tue' => 'Вторник',
                'wed' => 'Среда',
                'thu' => 'Четверг',
                'fri' => 'Пятница',
                'sat' => 'Суббота',
                'sun' => 'Воскресенье',
            ],
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

## Зависимости (Dependency)

Показывать поле только если другое поле имеет определённое значение:

```php
// Простая зависимость
WP_Field::make([
    'id'    => 'shop_name',
    'type'  => 'text',
    'label' => 'Название магазина',
]);

WP_Field::make([
    'id'    => 'shop_address',
    'type'  => 'text',
    'label' => 'Адрес магазина',
    'dependency' => [
        ['shop_name', '!=', ''],
    ],
]);

// Множественные условия (AND)
WP_Field::make([
    'id'    => 'delivery_address',
    'type'  => 'text',
    'label' => 'Адрес доставки',
    'dependency' => [
        ['delivery_type', '==', 'courier'],
        ['country', '==', 'ru'],
        'relation' => 'AND',
    ],
]);

// Множественные условия (OR)
WP_Field::make([
    'id'    => 'phone',
    'type'  => 'tel',
    'label' => 'Телефон',
    'dependency' => [
        ['contact_method', '==', 'phone'],
        ['contact_method', '==', 'whatsapp'],
        'relation' => 'OR',
    ],
]);

// Проверка наличия в массиве
WP_Field::make([
    'id'    => 'notification_text',
    'type'  => 'textarea',
    'label' => 'Текст уведомления',
    'dependency' => [
        ['notification_type', 'in', ['sms', 'push']],
    ],
]);
```

### Поддерживаемые операторы

- `==` — равно
- `!=` — не равно
- `>` — больше
- `>=` — больше или равно
- `<` — меньше
- `<=` — меньше или равно
- `in` — в массиве
- `not_in` — не в массиве
- `contains` — содержит
- `not_contains` — не содержит
- `empty` — пусто
- `not_empty` — не пусто

## Хранилища значений

По умолчанию значения сохраняются как post meta. Можно изменить:

```php
// Post meta (по умолчанию)
WP_Field::make(
    ['id' => 'field_id', 'type' => 'text', 'label' => 'Field'],
    true,
    'post'
);

// Option
WP_Field::make(
    ['id' => 'field_id', 'type' => 'text', 'label' => 'Field'],
    true,
    'options'
);

// Term meta
WP_Field::make(
    ['id' => 'field_id', 'type' => 'text', 'label' => 'Field'],
    true,
    'term',
    $term_id
);

// User meta
WP_Field::make(
    ['id' => 'field_id', 'type' => 'text', 'label' => 'Field'],
    true,
    'user',
    $user_id
);

// Comment meta
WP_Field::make(
    ['id' => 'field_id', 'type' => 'text', 'label' => 'Field'],
    true,
    'comment',
    $comment_id
);
```

## Атрибуты поля

```php
WP_Field::make([
    'id'                 => 'field_id',
    'type'               => 'text',
    'label'              => 'Название',
    'name'               => 'custom_name',      // Переопределить name атрибут
    'class'              => 'regular-text',     // CSS класс
    'placeholder'        => 'Введите значение',
    'value'              => 'default value',    // Явное значение
    'default'            => 'default value',    // Значение по умолчанию
    'desc'               => 'Описание поля',    // Описание под полем
    'readonly'           => false,              // Только для чтения
    'disabled'           => false,              // Отключено
    'custom_attributes'  => [                   // Кастомные атрибуты
        'data-custom' => 'value',
        'aria-label'  => 'Custom label',
    ],
]);
```

## Примеры использования в плагине

### В Settings Page

```php
<?php
// wp-content/plugins/woo2iiko/core/admin/SettingsManagerPage.php

class SettingsManagerPage {
    public function render_settings() {
        ?>
        <div class="wrap">
            <h1>Настройки Woo2iiko</h1>
            <form method="post" action="options.php">
                <?php settings_fields('woo2iiko_settings'); ?>
                
                <?php
                WP_Field::make([
                    'id'    => 'woo2iiko_api_key',
                    'type'  => 'text',
                    'label' => 'API Key',
                    'desc'  => 'Ключ API для подключения к iiko',
                ], true, 'options');
                ?>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
```

### В Meta Box

```php
<?php
add_action('add_meta_boxes', function() {
    add_meta_box(
        'woo2iiko_order_settings',
        'Настройки заказа',
        function($post) {
            WP_Field::make([
                'id'    => 'delivery_type',
                'type'  => 'radio',
                'label' => 'Тип доставки',
                'options' => [
                    'courier' => 'Курьер',
                    'pickup'  => 'Самовывоз',
                ],
            ]);
        },
        'post'
    );
});
```

## Сохранение значений

Значения автоматически сохраняются WordPress, если вы используете стандартные формы с `settings_fields()` и `do_settings_sections()`.

Для ручного сохранения:

```php
// Post meta
update_post_meta($post_id, 'field_id', $value);

// Option
update_option('field_id', $value);

// Term meta
update_term_meta($term_id, 'field_id', $value);

// User meta
update_user_meta($user_id, 'field_id', $value);

// Comment meta
update_comment_meta($comment_id, 'field_id', $value);
```

## Фильтры и хуки

```php
// Фильтр HTML поля перед выводом
apply_filters('wp_field_render', $html, $field);

// Действие после рендера поля
do_action('wp_field_after_render', $field);
```

## Совместимость

WP_Field совместим с WordPress 4.6+ и PHP 7.4+.

Старые алиасы полей продолжают работать:
- `title` → `label`
- `val` → `value`
- `attributes`, `attr`, `atts` → `custom_attributes`
- `image_picker`, `imagepicker` → `image_picker`
- `date_time` → `datetime`
