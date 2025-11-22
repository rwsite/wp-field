# План реализации полей средней сложности

## Обзор

10 типов полей средней сложности (~18 часов):
1. Accordion (2 часа)
2. Tabbed (2 часа)
3. Typography (2 часа)
4. Spacing (2 часа)
5. Dimensions (2 часа)
6. Border (2 часа)
7. Background (2 часа)
8. Link Color (1.5 часа)
9. Color Group (1.5 часа)
10. Image Select (1.5 часа)

---

Детальный план с полным кодом реализации для каждого типа поля находится в файле IMPLEMENTATION_PLAN_SIMPLE_FIELDS.md как пример.

Для каждого типа нужно:
1. Добавить в `init_field_types()`
2. Создать метод `render_*()`
3. Добавить CSS стили
4. Добавить JS логику (если нужна)
5. Создать Pest тесты

## Структура каждого типа

### Accordion
- Свёртываемые секции с иконками
- Поддержка вложенных полей
- Анимация открытия/закрытия

### Tabbed
- Горизонтальные вкладки
- Поддержка иконок
- Переключение без перезагрузки

### Typography
- Font family, size, weight
- Line height, text align
- Text transform, color
- Комбинированное поле

### Spacing
- Margin/Padding для 4 сторон
- Выбор единиц (px, em, rem, %)
- Визуальный редактор

### Dimensions
- Width/Height
- Выбор единиц
- Min/Max значения

### Border
- Style (solid, dashed, dotted)
- Width (px)
- Color (color picker)

### Background
- Color
- Image (media uploader)
- Position, Size, Repeat
- Attachment

### Link Color
- Normal, Hover, Active
- 3 color pickers

### Color Group
- Несколько связанных цветов
- Primary, Secondary, Accent

### Image Select
- Выбор из предустановленных изображений
- Как radio, но с картинками
- Grid layout

## Изменения в файлах

### WP_Field.php
- +10 типов в `init_field_types()`
- +10 методов `render_*()`
- ~800 строк кода

### assets/css/wp-field.css
- Стили для каждого типа
- ~600 строк CSS

### assets/js/wp-field.js
- Инициализация интерактивных элементов
- ~300 строк JS

### tests/
- Pest тесты для каждого типа
- ~400 строк тестов

## Итого

- **10 новых типов**
- **~800 строк PHP**
- **~600 строк CSS**
- **~300 строк JS**
- **~400 строк тестов**
- **Время: ~18 часов**
- **Результат: 44 типа полей (80% покрытия)**
