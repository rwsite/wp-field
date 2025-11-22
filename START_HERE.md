# 🚀 WP_Field v2.3 — НАЧНИТЕ ОТСЮДА

## 🎯 Интерактивная демонстрация

**Самый быстрый способ изучить WP_Field:**

👉 Откройте: **Инструменты → WP_Field Examples**  
👉 Или перейдите: `/wp-admin/tools.php?page=wp-field-examples`

Там вы увидите:
- ✅ Все 38 типов полей в действии
- ✅ Код для каждого примера
- ✅ Живую демонстрацию зависимостей
- ✅ Возможность тестировать и сохранять

---

## Что это?

**WP_Field** — универсальный генератор HTML полей для WordPress.

Позволяет создавать поля в админке с минимальным кодом:

```php
WP_Field::make([
    'id'    => 'field_id',
    'type'  => 'text',
    'label' => 'Field Label',
]);
```

## Что поддерживается?

✅ **38 типов полей** (text, select, radio, checkbox, editor, media, image, gallery, color, date, repeater, group, code_editor, icon, map, sortable и т.д.)

✅ **Система зависимостей** (12 операторов, AND/OR логика)

✅ **Все типы хранилищ** (post meta, options, term meta, user meta, comment meta)

✅ **Встроенные WP компоненты** (wp_editor, wp-color-picker, wp.media, CodeMirror, jQuery UI Sortable)

✅ **Без внешних зависимостей** (использует только встроенные WP скрипты)

## Быстрый старт (2 минуты)

### 1. Загрузите класс

```php
require_once plugin_dir_path(__FILE__) . 'lib/wp-field/WP_Field.php';
```

### 2. Создайте поле

```php
WP_Field::make([
    'id'    => 'shop_name',
    'type'  => 'text',
    'label' => 'Название магазина',
]);
```

### 3. Получите значение

```php
$value = get_option('shop_name');
```

## Примеры

### Select с опциями

```php
WP_Field::make([
    'id'      => 'delivery_type',
    'type'    => 'select',
    'label'   => 'Тип доставки',
    'options' => [
        'courier' => 'Курьер',
        'pickup'  => 'Самовывоз',
    ],
]);
```

### С зависимостью

```php
WP_Field::make([
    'id'    => 'delivery_address',
    'type'  => 'text',
    'label' => 'Адрес доставки',
    'dependency' => [
        ['delivery_type', '==', 'courier'],
    ],
]);
```

### Repeater (повторяющиеся элементы)

```php
WP_Field::make([
    'id'       => 'work_times',
    'type'     => 'repeater',
    'label'    => 'Время работы',
    'min'      => 1,
    'max'      => 7,
    'fields'   => [
        ['id' => 'day', 'type' => 'select', 'label' => 'День', 'options' => ['mon' => 'Пн', 'tue' => 'Вт']],
        ['id' => 'from', 'type' => 'time', 'label' => 'С'],
        ['id' => 'to', 'type' => 'time', 'label' => 'По'],
    ],
]);
```

## Документация

| Файл | Для кого | Описание |
|------|----------|---------|
| [README.md](README.md) | Все | Основная информация |
| [QUICKSTART.md](docs/QUICKSTART.md) | Разработчики | Примеры для каждого типа |
| [INTEGRATION_CHECKLIST.md](INTEGRATION_CHECKLIST.md) | Интеграция | Как интегрировать в плагин |
| [WP_FIELD_PLAN.md](docs/WP_FIELD_PLAN.md) | Архитектура | Как это работает |
| [STATUS.md](STATUS.md) | Статус | Что было сделано |
| [INDEX.md](INDEX.md) | Навигация | Полный индекс документации |

## Поддерживаемые типы полей

### Базовые
text, password, email, url, tel, number, range, hidden, textarea

### Выборные
select, multiselect, radio, checkbox, checkbox_group

### Продвинутые
editor, media, image, file, gallery, color, date, time, datetime

### Композитные
group, repeater

## Типы хранилищ

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

## Операторы зависимостей

```php
'dependency' => [
    ['field_id', '==', 'value'],        // равно
    ['field_id', '!=', 'value'],        // не равно
    ['field_id', '>', 10],              // больше
    ['field_id', 'in', ['a', 'b']],     // в массиве
    ['field_id', 'contains', 'text'],   // содержит
    ['field_id', 'empty', null],        // пусто
    'relation' => 'AND',                // AND или OR
]
```

## Проверка

### Синтаксис PHP
```bash
php -l WP_Field.php
```

### Unit-тесты
```bash
php tests/test-wp-field.php
```

## Часто задаваемые вопросы

**Q: Как использовать в Settings Page?**  
A: Смотрите [INTEGRATION_CHECKLIST.md](INTEGRATION_CHECKLIST.md#2-использование-в-settings-page)

**Q: Как работают зависимости?**  
A: Смотрите [QUICKSTART.md](docs/QUICKSTART.md#зависимости-dependency)

**Q: Какие типы полей есть?**  
A: Смотрите [README.md](README.md#поддерживаемые-типы-полей)

**Q: Как получить значение?**  
A: Смотрите [QUICKSTART.md](docs/QUICKSTART.md#получение-значения)

## Следующие шаги

1. Прочитайте [README.md](README.md)
2. Посмотрите примеры в [QUICKSTART.md](docs/QUICKSTART.md)
3. Интегрируйте в плагин по [INTEGRATION_CHECKLIST.md](INTEGRATION_CHECKLIST.md)

## Статус

✅ **ГОТОВО К ИСПОЛЬЗОВАНИЮ**

- Версия: 2.0.0
- Дата: 22 ноября 2025
- Статус: Production Ready
- Совместимость: WordPress 4.6+, PHP 7.4+

---

**Начните с [README.md](README.md) →**
