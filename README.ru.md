<p align="center">
  <img src="placeholder.svg" alt="WP_Field Logo" width="150" height="150">
</p>

<h1 align="center">WP_Field</h1>

<p align="center">
  <strong>Универсальный генератор HTML полей для WordPress</strong><br>
  Минималистичная, расширяемая библиотека для создания полей в WordPress с поддержкой:<br>
  52 типов полей, системы зависимостей, всех типов хранилищ и встроенных компонентов WP.
</p>

<p align="center">
  <a href="https://packagist.org/packages/rwsite/wp-field"><img src="https://img.shields.io/packagist/v/rwsite/wp-field.svg?style=flat-square" alt="Latest Version"></a>
  <img src="https://img.shields.io/badge/PHP-8.0+-blue.svg?style=flat-square" alt="PHP Version">
  <a href="https://github.com/rwsite/wp-field"><img src="https://img.shields.io/github/actions/workflow/status/rwsite/wp-field/tests.yml?branch=main&style=flat-square" alt="Build Status"></a>
  <a href="https://codecov.io/gh/rwsite/wp-field"><img src="https://img.shields.io/codecov/c/github/rwsite/wp-field?style=flat-square" alt="Code Coverage"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg?style=flat-square" alt="License"></a>
</p>

<p align="center">
  <a href="#возможности">Возможности</a> •
  <a href="#установка">Установка</a> •
  <a href="#быстрый-старт">Быстрый старт</a> •
  <a href="#типы-полей">Типы полей</a> •
  <a href="#примеры">Примеры</a> •
  <a href="#зависимости">Зависимости</a> •
  <a href="README.md">🇬🇧 English</a>
</p>

---

## Возможности

- 🚀 **52 типа полей** — Базовые, выборные, продвинутые, композитные и специализированные поля
- 🔗 **Система зависимостей** — 12 операторов с логикой AND/OR для видимости полей
- 📦 **Множественные хранилища** — Post meta, options, term meta, user meta, comment meta
- 🎨 **Компоненты WP** — Интеграция wp_editor, wp-color-picker, wp.media, CodeMirror
- 🔌 **Нулевые зависимости** — Использует только встроенные скрипты и компоненты WordPress
- 🌍 **i18n Ready** — Включены переводы (русский язык)
- 📊 **Интерактивная демонстрация** — Страница живых примеров в админ-панели WordPress

## Требования

- PHP 8.0+
- WordPress 4.6+

## Установка

1. Клонируйте или загрузите в `wp-content/plugins/wp-field`
2. Запустите `composer install`
3. Активируйте плагин

## Быстрый старт

### Простое текстовое поле

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

### Отправка полей

```php
use WP_Field\WP_Field;

// Отправить для вывода
WP_Field::make($field_config, true, 'post', $post_id);

// Сохранить в options
WP_Field::make($field_config, false, 'options');

// Term meta
WP_Field::make($field_config, false, 'term', $term_id);

// User meta
WP_Field::make($field_config, false, 'user', $user_id);

// Comment meta
WP_Field::make($field_config, false, 'comment', $comment_id);
```

## Типы полей (52)

### Базовые (9)
- `text` — Текстовый ввод
- `password` — Поле пароля
- `email` — Email ввод
- `url` — URL ввод
- `tel` — Телефонный ввод
- `number` — Числовой ввод
- `range` — Ползунок диапазона
- `hidden` — Скрытое поле
- `textarea` — Многострочный текст

### Выборные (5)
- `select` — Выпадающий список
- `multiselect` — Множественный выбор
- `radio` — Радиокнопки
- `checkbox` — Одиночный чекбокс
- `checkbox_group` — Группа чекбоксов

### Продвинутые (9)
- `editor` — wp_editor
- `media` — Медиа библиотека (ID или URL)
- `image` — Изображение с превью
- `file` — Загрузка файла
- `gallery` — Галерея изображений
- `color` — Выбор цвета
- `date` — Выбор даты
- `time` — Выбор времени
- `datetime` — Дата и время

### Композитные (2)
- `group` — Вложенные поля
- `repeater` — Повторяющиеся элементы

### Простые v2.1 (9)
- `switcher` — Переключатель вкл/выкл
- `spinner` — Счетчик чисел
- `button_set` — Выбор кнопками
- `slider` — Ползунок значения
- `heading` — Заголовок
- `subheading` — Подзаголовок
- `notice` — Уведомление (info/success/warning/error)
- `content` — Произвольный HTML контент
- `fieldset` — Группировка полей

### Средней сложности v2.2 (10)
- `accordion` — Сворачиваемые секции
- `tabbed` — Вкладки
- `typography` — Настройки типографии
- `spacing` — Контролы отступов
- `dimensions` — Контролы размеров
- `border` — Настройки границы
- `background` — Опции фона
- `link_color` — Цвета ссылок
- `color_group` — Группа цветов
- `image_select` — Выбор изображений

### Высокой сложности v2.3 (8)
- `code_editor` — Редактор кода с подсветкой синтаксиса
- `icon` — Выбор иконки из библиотеки
- `map` — Карта Google Maps с выбором координат
- `sortable` — Сортировка drag & drop
- `sorter` — Сортировка enabled/disabled
- `palette` — Палитра цветов
- `link` — Поле ссылки (URL + текст + target)
- `backup` — Экспорт/импорт настроек

## Примеры

### Зависимости

```php
// Показать поле только если другое поле имеет определенное значение
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

### Code Editor (v2.3)

```php
WP_Field::make([
    'id'     => 'custom_css',
    'type'   => 'code_editor',
    'label'  => 'Custom CSS',
    'mode'   => 'css', // css, javascript, php, html
    'height' => '400px',
]);
```

### Icon Picker (v2.3)

```php
WP_Field::make([
    'id'      => 'menu_icon',
    'type'    => 'icon',
    'label'   => 'Иконка меню',
    'library' => 'dashicons',
]);
```

### Map (v2.3)

```php
WP_Field::make([
    'id'      => 'location',
    'type'    => 'map',
    'label'   => 'Местоположение',
    'api_key' => 'YOUR_GOOGLE_MAPS_API_KEY',
    'zoom'    => 12,
    'center'  => ['lat' => 55.7558, 'lng' => 37.6173],
]);
```

### Sortable (v2.3)

```php
WP_Field::make([
    'id'      => 'menu_order',
    'type'    => 'sortable',
    'label'   => 'Порядок меню',
    'options' => [
        'home'     => 'Главная',
        'about'    => 'О нас',
        'services' => 'Услуги',
        'contact'  => 'Контакты',
    ],
]);
```

### Palette (v2.3)

```php
WP_Field::make([
    'id'       => 'color_scheme',
    'type'     => 'palette',
    'label'    => 'Цветовая схема',
    'palettes' => [
        'blue'   => ['#0073aa', '#005a87', '#003d82'],
        'green'  => ['#28a745', '#218838', '#1e7e34'],
        'red'    => ['#dc3545', '#c82333', '#bd2130'],
    ],
]);
```

### Link (v2.3)

```php
WP_Field::make([
    'id'    => 'cta_button',
    'type'  => 'link',
    'label' => 'CTA кнопка',
]);

// Получение значения:
$link = get_post_meta($post_id, 'cta_button', true);
// ['url' => '...', 'text' => '...', 'target' => '_blank']
```

### Accordion (v2.2)

```php
WP_Field::make([
    'id'       => 'settings_accordion',
    'type'     => 'accordion',
    'label'    => 'Настройки',
    'sections' => [
        [
            'title'  => 'Основные',
            'open'   => true,
            'fields' => [
                ['id' => 'title', 'type' => 'text', 'label' => 'Заголовок'],
            ],
        ],
        [
            'title'  => 'Дополнительные',
            'fields' => [
                ['id' => 'desc', 'type' => 'textarea', 'label' => 'Описание'],
            ],
        ],
    ],
]);
```

### Typography (v2.2)

```php
WP_Field::make([
    'id'    => 'heading_typography',
    'type'  => 'typography',
    'label' => 'Типография заголовков',
]);

// Сохраняется как:
// [
//     'font_family' => 'Arial',
//     'font_size' => '24',
//     'font_weight' => '700',
//     'line_height' => '1.5',
//     'text_align' => 'center',
//     'color' => '#333333'
// ]
```

## Операторы зависимостей

- `==` — Равно
- `!=` — Не равно
- `>`, `>=`, `<`, `<=` — Сравнение
- `in` — В массиве
- `not_in` — Не в массиве
- `contains` — Содержит
- `not_contains` — Не содержит
- `empty` — Пусто
- `not_empty` — Не пусто

## Интерактивная демонстрация

**Посмотрите все 52 типа полей в действии:**

👉 **Инструменты → WP_Field Examples**  
или  
👉 `/wp-admin/tools.php?page=wp-field-examples`

Страница включает:
- ✅ Все типы полей с живыми примерами
- ✅ Код для каждого поля
- ✅ Демонстрацию системы зависимостей
- ✅ Возможность сохранения и тестирования

## Расширяемость

### Добавление своих типов полей

```php
add_filter('wp_field_types', function($types) {
    $types['custom_type'] = ['render_custom', ['default' => 'value']];
    return $types;
});
```

### Добавление библиотек иконок

```php
add_filter('wp_field_icon_library', function($icons, $library) {
    if ($library === 'fontawesome') {
        return ['fa-home', 'fa-user', 'fa-cog', ...];
    }
    return $icons;
}, 10, 2);
```

### Кастомное получение значений

```php
add_filter('wp_field_get_value', function($value, $storage_type, $key, $id, $field) {
    if ($storage_type === 'custom') {
        return get_custom_value($key, $id);
    }
    return $value;
}, 10, 5);
```

## Changelog

Смотрите **[CHANGELOG.md](CHANGELOG.md)** для подробной истории версий.

## Статистика проекта

- **Строк PHP:** 2705 (WP_Field.php)
- **Строк JS:** 1222 (wp-field.js)
- **Строк CSS:** 1839 (wp-field.css)
- **Типов полей:** 52+
- **Операторов зависимостей:** 12
- **Типов хранилищ:** 5
- **Внешних зависимостей:** 0

## Совместимость

- **WordPress:** 4.6+
- **PHP:** 7.4+
- **Зависимости:** jQuery, jQuery UI Sortable, встроенные компоненты WordPress
- **Браузеры:** Chrome, Firefox, Safari, Edge (последние 2 версии)

## Производительность

- Минимальный размер CSS: ~20KB
- Минимальный размер JS: ~15KB
- Lazy loading для тяжелых компонентов (CodeMirror, Google Maps)
- Оптимизированные селекторы и события

## Лицензия

GPL v2 или выше

## Автор

Aleksei Tikhomirov (https://rwsite.ru)
