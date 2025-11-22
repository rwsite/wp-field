# WP_Field Tests v2.4.11

Тесты для проверки функциональности WP_Field v2.4.11 со всеми 38 типами полей.

## Структура тестов

```
tests/
├── Feature/
│   ├── FieldRenderingTest.php      # Тесты рендера всех типов полей
│   ├── DependencyTest.php          # Тесты системы зависимостей
│   ├── ChoiceFieldsTest.php        # Тесты полей выбора (select, radio, checkbox)
│   └── CompositeFieldsTest.php     # Тесты композитных полей (group, repeater)
├── Unit/
│   ├── FieldInitializationTest.php # Тесты инициализации и валидации
│   └── StorageTypesTest.php        # Тесты типов хранилищ
└── README.md                        # Этот файл
```

## Запуск тестов

### 🚀 Быстрый способ (рекомендуется)

```bash
# Запустить все тесты
bash run-tests.sh

# Или напрямую
php tests/test-wp-field-v2.4.php
```

### Запуск всех тестов (Pest)

```bash
composer install
./vendor/bin/pest
```

### Запуск конкретного файла

```bash
./vendor/bin/pest tests/Feature/FieldRenderingTest.php
```

### Запуск с покрытием кода

```bash
./vendor/bin/pest --coverage
```

### Запуск с фильтром

```bash
./vendor/bin/pest --filter "accordion"
```

## Тестовые сценарии

### Feature Tests (FieldRenderingTest.php)

- ✅ Рендер text поля
- ✅ Рендер select поля
- ✅ Рендер radio поля
- ✅ Рендер checkbox поля
- ✅ Рендер textarea поля
- ✅ Рендер number поля
- ✅ Рендер email поля
- ✅ Рендер color поля
- ✅ Рендер date поля
- ✅ Рендер time поля
- ✅ Поле с placeholder
- ✅ Поле с description
- ✅ Поле с custom class
- ✅ Поле с custom attributes
- ✅ Readonly поле
- ✅ Disabled поле

### Feature Tests (DependencyTest.php)

- ✅ Скрытие поля при неудовлетворённой зависимости
- ✅ Показ поля при удовлетворённой зависимости
- ✅ Рендер data-dependency атрибута
- ✅ Множественные зависимости с AND
- ✅ Множественные зависимости с OR
- ✅ Оператор `in`
- ✅ Оператор `contains`
- ✅ Оператор `empty`
- ✅ Операторы сравнения (==, !=, >, >=, <, <=)

### Feature Tests (ChoiceFieldsTest.php)

- ✅ Select с опциями
- ✅ Multiselect
- ✅ Radio с опциями
- ✅ Checkbox group
- ✅ Select с выбранным значением
- ✅ Radio с отмеченным значением
- ✅ Checkbox group с множественными значениями
- ✅ Parse options
- ✅ Radio group с labels

### Feature Tests (CompositeFieldsTest.php)

- ✅ Group поле
- ✅ Repeater поле
- ✅ Repeater с min/max
- ✅ Repeater с кнопкой Add
- ✅ Group с вложенными полями
- ✅ Repeater с select опциями

### Unit Tests (FieldInitializationTest.php)

- ✅ Инициализация реестра типов
- ✅ Алиас `title` для `label`
- ✅ Алиас `val` для `value`
- ✅ Алиас `attributes` для `custom_attributes`
- ✅ Статический метод `make()`
- ✅ Вывод через `make()` с `output=true`
- ✅ Валидация обязательных полей
- ✅ Дефолтный тип хранилища
- ✅ Различные типы хранилищ
- ✅ Поле с default значением
- ✅ Поле с explicit значением
- ✅ Поле с опциями
- ✅ Поле с вложенными полями

### Unit Tests (StorageTypesTest.php)

- ✅ Post storage type
- ✅ Options storage type
- ✅ Term storage type
- ✅ User storage type
- ✅ Comment storage type
- ✅ Рендер с post storage
- ✅ Рендер с options storage
- ✅ Рендер с term storage
- ✅ Рендер с user storage
- ✅ Рендер с comment storage

## Тесты v2.4.11

### Основные тесты

- ✅ Field types registry initialization
- ✅ Text field rendering
- ✅ Select field rendering
- ✅ Dependency evaluation

### Тесты v2.2 (Accordion, Tabbed и т.д.)

- ✅ Accordion field rendering
- ✅ Tabbed field rendering
- ✅ Icon field rendering
- ✅ Repeater field rendering
- ✅ Color picker field rendering

### Тесты v2.3 (Code Editor, Map и т.д.)

- ✅ Code editor field rendering
- ✅ Map field rendering
- ✅ Sortable field rendering
- ✅ Palette field rendering

### Тесты состояния (localStorage)

- ✅ Accordion state persistence
- ✅ Tabbed state persistence

## Статистика

| Категория | Количество |
|-----------|-----------|
| Основные тесты | 4 |
| v2.2 тесты | 5 |
| v2.3 тесты | 4 |
| Тесты состояния | 2 |
| **ИТОГО** | **15** |

## Покрытие

Тесты покрывают:

- ✅ Все 38 типов полей
- ✅ Система зависимостей (12 операторов)
- ✅ Все типы хранилищ (5)
- ✅ Композитные поля (group, repeater)
- ✅ Поля выбора (select, radio, checkbox)
- ✅ Валидация и инициализация
- ✅ Сохранение состояния (localStorage)
- ✅ Атрибуты и классы

## Примеры запуска

### Запустить только Feature тесты

```bash
./vendor/bin/pest tests/Feature
```

### Запустить только Unit тесты

```bash
./vendor/bin/pest tests/Unit
```

### Запустить с подробным выводом

```bash
./vendor/bin/pest -v
```

### Запустить с остановкой на первой ошибке

```bash
./vendor/bin/pest --bail
```

## Добавление новых тестов

Для добавления новых тестов:

1. Создайте файл в `tests/Feature/` или `tests/Unit/`
2. Используйте существующие тесты как шаблон
3. Запустите тесты: `./vendor/bin/pest`

Пример:

```php
<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class MyNewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_does_something(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ], false);

        expect($html)->toContain('wp-field');
    }
}
```

## Требования

- PHP 8.0+
- Pest 2.0+
- PHPUnit 10.0+

## Дополнительно

- Все тесты используют Pest assertions
- Тесты не требуют WordPress окружения (standalone)
- Тесты проверяют HTML рендер, не функциональность БД
