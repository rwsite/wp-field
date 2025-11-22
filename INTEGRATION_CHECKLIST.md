# WP_Field v2.0 — Чек-лист интеграции

## Перед использованием

- [x] Синтаксис PHP проверен (нет ошибок)
- [x] Класс полностью переписан (v2.0)
- [x] Все типы полей реализованы
- [x] Система зависимостей работает
- [x] Assets подключены (JS/CSS)
- [x] Документация полная
- [x] Тесты готовы

## Интеграция в плагин

### 1. Загрузка класса

```php
// В главном файле плагина или в нужном месте
require_once plugin_dir_path(__FILE__) . 'lib/wp-field/WP_Field.php';
```

### 2. Использование в Settings Page

```php
<?php
// core/admin/SettingsManagerPage.php

class SettingsManagerPage {
    public function render_settings() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('woo2iiko_settings'); ?>
                
                <!-- Используем WP_Field для каждого поля -->
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

### 3. Использование в Meta Box

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
            
            WP_Field::make([
                'id'    => 'delivery_address',
                'type'  => 'text',
                'label' => 'Адрес доставки',
                'dependency' => [
                    ['delivery_type', '==', 'courier'],
                ],
            ]);
        },
        'post'
    );
});
```

## Проверка функциональности

### Тест 1: Базовое поле

```php
WP_Field::make([
    'id'    => 'test_field',
    'type'  => 'text',
    'label' => 'Test Field',
]);
```

**Ожидаемый результат:** Поле отрендерено с label и wrapper'ом

### Тест 2: Select с опциями

```php
WP_Field::make([
    'id'      => 'test_select',
    'type'    => 'select',
    'label'   => 'Test Select',
    'options' => ['a' => 'Option A', 'b' => 'Option B'],
]);
```

**Ожидаемый результат:** Select с двумя опциями

### Тест 3: Зависимость

```php
WP_Field::make([
    'id'      => 'enable_feature',
    'type'    => 'checkbox',
    'label'   => 'Enable Feature',
]);

WP_Field::make([
    'id'    => 'feature_settings',
    'type'  => 'text',
    'label' => 'Feature Settings',
    'dependency' => [
        ['enable_feature', '==', '1'],
    ],
]);
```

**Ожидаемый результат:** Второе поле скрыто по умолчанию, показывается при отметке checkbox

### Тест 4: Repeater

```php
WP_Field::make([
    'id'       => 'test_repeater',
    'type'     => 'repeater',
    'label'    => 'Test Repeater',
    'min'      => 1,
    'max'      => 5,
    'fields'   => [
        ['id' => 'name', 'type' => 'text', 'label' => 'Name'],
        ['id' => 'email', 'type' => 'email', 'label' => 'Email'],
    ],
]);
```

**Ожидаемый результат:** Repeater с кнопками Add/Remove

## Сохранение значений

### Для Options

```php
// Значение автоматически сохраняется при использовании
// settings_fields() и do_settings_sections()

// Или вручную:
update_option('field_id', $value);
```

### Для Post Meta

```php
// Значение автоматически сохраняется при использовании
// WordPress meta box API

// Или вручную:
update_post_meta($post_id, 'field_id', $value);
```

## Получение значений

```php
// Option
$value = get_option('field_id');

// Post meta
$value = get_post_meta($post_id, 'field_id', true);

// Term meta
$value = get_term_meta($term_id, 'field_id', true);

// User meta
$value = get_user_meta($user_id, 'field_id', true);

// Comment meta
$value = get_comment_meta($comment_id, 'field_id', true);
```

## Стилизация

### Кастомные CSS классы

```php
WP_Field::make([
    'id'    => 'field_id',
    'type'  => 'text',
    'label' => 'Field',
    'class' => 'my-custom-class',
]);
```

### Переопределение стилей

```css
/* Переопределяем стили WP_Field */
.wp-field input[type="text"] {
    /* ваши стили */
}

.wp-field.is-hidden {
    /* стили для скрытых полей */
}
```

## Отладка

### Включение debug режима

```php
// В wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Логи будут в wp-content/debug.log
```

### Проверка рендера

```php
// Получить HTML без вывода
$html = WP_Field::make($field_config, false);
var_dump($html);
```

### Проверка зависимостей

```php
// В браузере, консоль:
console.log(WPField); // Должен быть объект с методами
```

## Возможные проблемы и решения

### Проблема: Assets не подключаются

**Решение:**
```php
// Убедитесь, что вы в админке
if (!is_admin()) {
    return;
}

// Проверьте, что WP_Field::make() вызывается в админке
```

### Проблема: Зависимости не работают

**Решение:**
```php
// Убедитесь, что jQuery загружен
wp_enqueue_script('jquery');

// Проверьте консоль браузера на ошибки
// Убедитесь, что wp-field.js загружен
```

### Проблема: Значения не сохраняются

**Решение:**
```php
// Убедитесь, что используете правильный storage_type
WP_Field::make($field, true, 'options'); // для options

// Или вручную сохраняйте
update_option('field_id', $_POST['field_id']);
```

## Документация

- **README.md** — основная документация
- **QUICKSTART.md** — примеры использования
- **WP_FIELD_PLAN.md** — архитектура
- **IMPLEMENTATION_SUMMARY.md** — резюме реализации
- **tests/test-wp-field.php** — unit-тесты

## Поддержка

Если возникают вопросы:

1. Проверьте документацию в `docs/`
2. Посмотрите примеры в `QUICKSTART.md`
3. Запустите тесты: `php tests/test-wp-field.php`
4. Проверьте консоль браузера (F12)
5. Проверьте логи WordPress: `wp-content/debug.log`

## Готово к использованию ✅

WP_Field v2.0 полностью готов к интеграции в плагин Woo2iiko.
