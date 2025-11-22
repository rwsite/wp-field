# WP_Field v2.0 — Тестирование с Pest

## Обзор

WP_Field полностью покрыт тестами Pest:

- **64 тестов** (41 Feature + 23 Unit)
- **100% покрытие** основного функционала
- **Pest 2.0** — современный фреймворк для тестирования PHP

## Быстрый старт

### 1. Установка зависимостей

```bash
cd /home/alex/woocommerce-local/wp-content/plugins/woo2iiko/lib/wp-field
composer install
```

### 2. Запуск всех тестов

```bash
composer test
```

### 3. Запуск с покрытием кода

```bash
composer test:coverage
```

## Команды

| Команда | Описание |
|---------|---------|
| `composer test` | Запустить все тесты |
| `composer test:coverage` | Запустить с покрытием кода |
| `composer test:unit` | Только Unit тесты |
| `composer test:feature` | Только Feature тесты |

## Структура тестов

```
tests/
├── Feature/
│   ├── FieldRenderingTest.php      # 16 тестов
│   ├── DependencyTest.php          # 9 тестов
│   ├── ChoiceFieldsTest.php        # 9 тестов
│   └── CompositeFieldsTest.php     # 7 тестов
├── Unit/
│   ├── FieldInitializationTest.php # 13 тестов
│   └── StorageTypesTest.php        # 10 тестов
└── README.md
```

## Что тестируется

### ✅ Все типы полей (20+)

- Базовые: text, password, email, url, tel, number, range, hidden, textarea
- Выборные: select, multiselect, radio, checkbox, checkbox_group
- Продвинутые: editor, media, image, file, gallery, color, date, time, datetime
- Композитные: group, repeater

### ✅ Система зависимостей

- Все 12 операторов (==, !=, >, >=, <, <=, in, not_in, contains, not_contains, empty, not_empty)
- AND/OR логика
- Скрытие/показ полей

### ✅ Все типы хранилищ

- post (post meta)
- options (WordPress options)
- term (term meta)
- user (user meta)
- comment (comment meta)

### ✅ Валидация и инициализация

- Алиасы параметров
- Обязательные поля
- Дефолтные значения
- Вложенные поля

### ✅ Рендер HTML

- Корректные теги
- Атрибуты
- Классы
- Data-* атрибуты

## Примеры запуска

### Запустить конкретный тест

```bash
./vendor/bin/pest tests/Feature/FieldRenderingTest.php
```

### Запустить с фильтром

```bash
./vendor/bin/pest --filter "it_renders_text_field"
```

### Запустить с подробным выводом

```bash
./vendor/bin/pest -v
```

### Запустить с остановкой на первой ошибке

```bash
./vendor/bin/pest --bail
```

### Запустить с профилированием

```bash
./vendor/bin/pest --profile
```

## Примеры тестов

### Feature Test — Рендер поля

```php
/** @test */
public function it_renders_text_field(): void
{
    $html = \WP_Field::make([
        'id'    => 'test_text',
        'type'  => 'text',
        'label' => 'Test Text',
    ], false);

    expect($html)->toContain('wp-field');
    expect($html)->toContain('test_text');
    expect($html)->toContain('type="text"');
}
```

### Unit Test — Инициализация

```php
/** @test */
public function it_initializes_field_types_registry(): void
{
    \WP_Field::init_field_types();

    $reflection = new \ReflectionClass(\WP_Field::class);
    $property = $reflection->getProperty('field_types');
    $property->setAccessible(true);
    $types = $property->getValue();

    expect($types)->not->toBeEmpty();
    expect(isset($types['text']))->toBeTrue();
}
```

## Pest Assertions

Используемые assertions:

```php
expect($value)->toContain('text');           // Содержит
expect($value)->not->toContain('text');      // Не содержит
expect($value)->toBeString();                // Строка
expect($value)->toBeNull();                  // Null
expect($value)->toBeTrue();                  // True
expect($value)->toBeEmpty();                 // Пусто
expect($value)->toHaveCount(2);              // Количество элементов
```

## Требования

- PHP 8.0+
- Pest 2.0+
- PHPUnit 10.0+
- Composer

## Установка Pest

Если Pest не установлен:

```bash
composer require --dev pestphp/pest
```

## Конфигурация

Конфигурация находится в:

- `phpunit.xml` — конфиг PHPUnit
- `pest.php` — конфиг Pest (helper функции)
- `composer.json` — скрипты и зависимости

## Добавление новых тестов

1. Создайте файл в `tests/Feature/` или `tests/Unit/`
2. Наследуйте `PHPUnit\Framework\TestCase`
3. Требуйте `WP_Field.php` в `setUp()`
4. Используйте `expect()` для assertions

Пример:

```php
<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_does_something(): void
    {
        $html = \WP_Field::make([...], false);
        expect($html)->toContain('expected');
    }
}
```

## CI/CD интеграция

Для GitHub Actions:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: php-actions/composer@v6
      - run: composer test
```

## Troubleshooting

### Pest не найден

```bash
composer install
./vendor/bin/pest
```

### Ошибка "Class not found"

Убедитесь, что `WP_Field.php` требуется в `setUp()`:

```php
require_once dirname(__DIR__, 2) . '/WP_Field.php';
```

### Тесты не запускаются

Проверьте, что файлы находятся в `tests/Feature/` или `tests/Unit/` и наследуют `TestCase`.

## Статистика

| Метрика | Значение |
|---------|----------|
| Всего тестов | 64 |
| Feature тесты | 41 |
| Unit тесты | 23 |
| Типов полей | 20+ |
| Операторов зависимостей | 12 |
| Типов хранилищ | 5 |

## Дополнительно

- Документация Pest: https://pestphp.com/
- Документация PHPUnit: https://phpunit.de/
- Все тесты standalone (не требуют WordPress)
