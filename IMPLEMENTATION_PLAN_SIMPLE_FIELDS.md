# План реализации простых типов полей

## Обзор

Реализация 9 типов полей низкой сложности (~5 часов работы):
1. Switcher (30 мин)
2. Spinner (30 мин)
3. Button Set (1 час)
4. Slider (1 час)
5. Heading (15 мин)
6. Subheading (15 мин)
7. Notice (15 мин)
8. Content (15 мин)
9. Fieldset (30 мин)

---

## 1. Switcher (переключатель on/off)

### Описание
Переключатель вкл/выкл как checkbox, но с более красивым UI.

### Параметры
```php
[
    'id'      => 'enable_feature',
    'type'    => 'switcher',
    'label'   => 'Enable Feature',
    'desc'    => 'Turn on/off this feature',
    'default' => false,
    'text_on' => 'On',   // Текст для включённого состояния
    'text_off' => 'Off', // Текст для выключенного состояния
]
```

### Реализация

#### PHP (WP_Field.php)
```php
// В init_field_types()
self::$field_types['switcher'] = ['render_switcher', ['text_on' => 'On', 'text_off' => 'Off']];

// Метод рендера
private function render_switcher(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $checked = !empty($value) ? 'checked' : '';
    
    $text_on = $field['text_on'] ?? 'On';
    $text_off = $field['text_off'] ?? 'Off';
    
    $html = sprintf(
        '<label class="wp-field-switcher">
            <input type="checkbox" name="%s" value="1" %s %s %s />
            <span class="wp-field-switcher-slider">
                <span class="wp-field-switcher-on">%s</span>
                <span class="wp-field-switcher-off">%s</span>
            </span>
        </label>',
        esc_attr($field['name'] ?? $field['id']),
        $checked,
        $this->get_field_attributes($field),
        $this->get_readonly_disabled($field),
        esc_html($text_on),
        esc_html($text_off)
    );
    
    return $html;
}
```

#### CSS (wp-field.css)
```css
.wp-field-switcher {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
    cursor: pointer;
}

.wp-field-switcher input {
    opacity: 0;
    width: 0;
    height: 0;
}

.wp-field-switcher-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 30px;
    transition: 0.3s;
}

.wp-field-switcher-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    border-radius: 50%;
    transition: 0.3s;
}

.wp-field-switcher input:checked + .wp-field-switcher-slider {
    background-color: #0073aa;
}

.wp-field-switcher input:checked + .wp-field-switcher-slider:before {
    transform: translateX(30px);
}

.wp-field-switcher-on,
.wp-field-switcher-off {
    position: absolute;
    font-size: 10px;
    font-weight: bold;
    color: white;
    top: 50%;
    transform: translateY(-50%);
    transition: opacity 0.3s;
}

.wp-field-switcher-on {
    left: 8px;
    opacity: 0;
}

.wp-field-switcher-off {
    right: 8px;
}

.wp-field-switcher input:checked ~ .wp-field-switcher-on {
    opacity: 1;
}

.wp-field-switcher input:checked ~ .wp-field-switcher-off {
    opacity: 0;
}
```

---

## 2. Spinner (счётчик с кнопками)

### Описание
Числовое поле с кнопками увеличения/уменьшения.

### Параметры
```php
[
    'id'    => 'quantity',
    'type'  => 'spinner',
    'label' => 'Quantity',
    'min'   => 0,
    'max'   => 100,
    'step'  => 1,
    'unit'  => 'шт', // Единица измерения
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['spinner'] = ['render_spinner', []];

// Метод рендера
private function render_spinner(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    
    $min = $field['min'] ?? 0;
    $max = $field['max'] ?? 100;
    $step = $field['step'] ?? 1;
    $unit = $field['unit'] ?? '';
    
    $html = sprintf(
        '<div class="wp-field-spinner">
            <button type="button" class="wp-field-spinner-down" data-step="%s">-</button>
            <input type="number" name="%s" value="%s" min="%s" max="%s" step="%s" %s %s />
            %s
            <button type="button" class="wp-field-spinner-up" data-step="%s">+</button>
        </div>',
        esc_attr($step),
        esc_attr($field['name'] ?? $field['id']),
        esc_attr($value),
        esc_attr($min),
        esc_attr($max),
        esc_attr($step),
        $this->get_field_attributes($field),
        $this->get_readonly_disabled($field),
        $unit ? '<span class="wp-field-spinner-unit">' . esc_html($unit) . '</span>' : '',
        esc_attr($step)
    );
    
    return $html;
}
```

#### CSS
```css
.wp-field-spinner {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.wp-field-spinner input {
    width: 80px;
    text-align: center;
}

.wp-field-spinner-down,
.wp-field-spinner-up {
    width: 30px;
    height: 30px;
    padding: 0;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.2s;
}

.wp-field-spinner-down:hover,
.wp-field-spinner-up:hover {
    background: #005a87;
}

.wp-field-spinner-unit {
    margin-left: 5px;
    color: #666;
}
```

#### JS (wp-field.js)
```javascript
// В WPField.init()
this.initSpinner();

// Метод инициализации
initSpinner: function() {
    $(document).on('click', '.wp-field-spinner-up', function(e) {
        e.preventDefault();
        const $input = $(this).siblings('input');
        const step = parseFloat($(this).data('step')) || 1;
        const max = parseFloat($input.attr('max'));
        const current = parseFloat($input.val()) || 0;
        const newValue = current + step;
        
        if (!max || newValue <= max) {
            $input.val(newValue).trigger('change');
        }
    });
    
    $(document).on('click', '.wp-field-spinner-down', function(e) {
        e.preventDefault();
        const $input = $(this).siblings('input');
        const step = parseFloat($(this).data('step')) || 1;
        const min = parseFloat($input.attr('min'));
        const current = parseFloat($input.val()) || 0;
        const newValue = current - step;
        
        if (typeof min === 'undefined' || newValue >= min) {
            $input.val(newValue).trigger('change');
        }
    });
}
```

---

## 3. Button Set (группа кнопок для выбора)

### Описание
Как radio, но с кнопками вместо радиокнопок.

### Параметры
```php
[
    'id'      => 'alignment',
    'type'    => 'button_set',
    'label'   => 'Text Alignment',
    'options' => [
        'left'   => 'Left',
        'center' => 'Center',
        'right'  => 'Right',
    ],
    'multiple' => false, // Для множественного выбора
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['button_set'] = ['render_button_set', []];

// Метод рендера
private function render_button_set(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $options = $field['options'] ?? [];
    $multiple = !empty($field['multiple']);
    
    if (empty($options)) {
        return '<p class="description">No options provided</p>';
    }
    
    $html = '<div class="wp-field-button-set">';
    
    if ($multiple) {
        $values = is_array($value) ? $value : ($value ? [$value] : []);
        foreach ($options as $key => $label) {
            $checked = in_array($key, $values) ? 'checked' : '';
            $html .= sprintf(
                '<label class="wp-field-button-set-item %s">
                    <input type="checkbox" name="%s[]" value="%s" %s %s />
                    <span>%s</span>
                </label>',
                $checked ? 'active' : '',
                esc_attr($field['name'] ?? $field['id']),
                esc_attr($key),
                $checked,
                $this->get_readonly_disabled($field),
                esc_html($label)
            );
        }
    } else {
        foreach ($options as $key => $label) {
            $checked = $key == $value ? 'checked' : '';
            $html .= sprintf(
                '<label class="wp-field-button-set-item %s">
                    <input type="radio" name="%s" value="%s" %s %s />
                    <span>%s</span>
                </label>',
                $checked ? 'active' : '',
                esc_attr($field['name'] ?? $field['id']),
                esc_attr($key),
                $checked,
                $this->get_readonly_disabled($field),
                esc_html($label)
            );
        }
    }
    
    $html .= '</div>';
    
    return $html;
}
```

#### CSS
```css
.wp-field-button-set {
    display: inline-flex;
    gap: 0;
}

.wp-field-button-set-item {
    position: relative;
    margin: 0;
}

.wp-field-button-set-item input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.wp-field-button-set-item span {
    display: block;
    padding: 8px 15px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-right: none;
    cursor: pointer;
    transition: all 0.2s;
}

.wp-field-button-set-item:first-child span {
    border-radius: 4px 0 0 4px;
}

.wp-field-button-set-item:last-child span {
    border-radius: 0 4px 4px 0;
    border-right: 1px solid #ddd;
}

.wp-field-button-set-item.active span,
.wp-field-button-set-item:hover span {
    background: #0073aa;
    color: white;
    border-color: #0073aa;
}

.wp-field-button-set-item.active + .wp-field-button-set-item span {
    border-left-color: #0073aa;
}
```

#### JS
```javascript
// В WPField.init()
this.initButtonSet();

// Метод инициализации
initButtonSet: function() {
    $(document).on('change', '.wp-field-button-set input', function() {
        const $item = $(this).closest('.wp-field-button-set-item');
        const $set = $item.closest('.wp-field-button-set');
        
        if ($(this).is(':radio')) {
            $set.find('.wp-field-button-set-item').removeClass('active');
        }
        
        if ($(this).is(':checked')) {
            $item.addClass('active');
        } else {
            $item.removeClass('active');
        }
    });
}
```

---

## 4. Slider (слайдер для выбора значения)

### Описание
Слайдер с красивым UI (улучшенная версия range).

### Параметры
```php
[
    'id'    => 'volume',
    'type'  => 'slider',
    'label' => 'Volume',
    'min'   => 0,
    'max'   => 100,
    'step'  => 1,
    'unit'  => '%',
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['slider'] = ['render_slider', []];

// Метод рендера
private function render_slider(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    
    $min = $field['min'] ?? 0;
    $max = $field['max'] ?? 100;
    $step = $field['step'] ?? 1;
    $unit = $field['unit'] ?? '';
    
    $html = sprintf(
        '<div class="wp-field-slider">
            <input type="range" name="%s" value="%s" min="%s" max="%s" step="%s" %s %s />
            <span class="wp-field-slider-value">%s%s</span>
        </div>',
        esc_attr($field['name'] ?? $field['id']),
        esc_attr($value),
        esc_attr($min),
        esc_attr($max),
        esc_attr($step),
        $this->get_field_attributes($field),
        $this->get_readonly_disabled($field),
        esc_html($value),
        esc_html($unit)
    );
    
    return $html;
}
```

#### CSS
```css
.wp-field-slider {
    display: flex;
    align-items: center;
    gap: 15px;
}

.wp-field-slider input[type="range"] {
    flex: 1;
    height: 6px;
    -webkit-appearance: none;
    appearance: none;
    background: #ddd;
    border-radius: 3px;
    outline: none;
}

.wp-field-slider input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #0073aa;
    border-radius: 50%;
    cursor: pointer;
}

.wp-field-slider input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #0073aa;
    border-radius: 50%;
    cursor: pointer;
    border: none;
}

.wp-field-slider-value {
    min-width: 50px;
    font-weight: 500;
    color: #333;
}
```

#### JS
```javascript
// В WPField.init()
this.initSlider();

// Метод инициализации
initSlider: function() {
    $(document).on('input', '.wp-field-slider input[type="range"]', function() {
        const value = $(this).val();
        const unit = $(this).closest('.wp-field-slider').find('.wp-field-slider-value').text().replace(/[\d.]/g, '');
        $(this).siblings('.wp-field-slider-value').text(value + unit);
    });
}
```

---

## 5. Heading (заголовок)

### Описание
Просто заголовок для разделения секций.

### Параметры
```php
[
    'type'    => 'heading',
    'content' => 'Section Title',
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['heading'] = ['render_heading', []];

// Метод рендера
private function render_heading(): string
{
    $field = $this->field;
    $content = $field['content'] ?? $field['label'] ?? '';
    
    return sprintf(
        '<h3 class="wp-field-heading">%s</h3>',
        esc_html($content)
    );
}
```

#### CSS
```css
.wp-field-heading {
    margin: 20px 0 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #0073aa;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}
```

---

## 6. Subheading (подзаголовок)

### Описание
Подзаголовок меньшего размера.

### Параметры
```php
[
    'type'    => 'subheading',
    'content' => 'Subsection Title',
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['subheading'] = ['render_subheading', []];

// Метод рендера
private function render_subheading(): string
{
    $field = $this->field;
    $content = $field['content'] ?? $field['label'] ?? '';
    
    return sprintf(
        '<h4 class="wp-field-subheading">%s</h4>',
        esc_html($content)
    );
}
```

#### CSS
```css
.wp-field-subheading {
    margin: 15px 0 10px;
    font-size: 14px;
    font-weight: 600;
    color: #555;
}
```

---

## 7. Notice (уведомление)

### Описание
Информационное сообщение с разными стилями.

### Параметры
```php
[
    'type'    => 'notice',
    'style'   => 'info', // info, success, warning, error
    'content' => 'This is an information message',
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['notice'] = ['render_notice', ['style' => 'info']];

// Метод рендера
private function render_notice(): string
{
    $field = $this->field;
    $content = $field['content'] ?? $field['label'] ?? '';
    $style = $field['style'] ?? 'info';
    
    return sprintf(
        '<div class="wp-field-notice notice notice-%s">
            <p>%s</p>
        </div>',
        esc_attr($style),
        wp_kses_post($content)
    );
}
```

#### CSS
```css
.wp-field-notice {
    padding: 10px 15px;
    margin: 10px 0;
    border-left: 4px solid;
    background: #f9f9f9;
}

.wp-field-notice.notice-info {
    border-color: #0073aa;
}

.wp-field-notice.notice-success {
    border-color: #46b450;
}

.wp-field-notice.notice-warning {
    border-color: #ffb900;
}

.wp-field-notice.notice-error {
    border-color: #dc3232;
}

.wp-field-notice p {
    margin: 0;
    font-size: 13px;
}
```

---

## 8. Content (произвольный контент)

### Описание
Вывод произвольного HTML/текста.

### Параметры
```php
[
    'type'    => 'content',
    'content' => '<p>Custom HTML content</p>',
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['content'] = ['render_content', []];

// Метод рендера
private function render_content(): string
{
    $field = $this->field;
    $content = $field['content'] ?? '';
    
    return sprintf(
        '<div class="wp-field-content">%s</div>',
        wp_kses_post($content)
    );
}
```

#### CSS
```css
.wp-field-content {
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}
```

---

## 9. Fieldset (группировка полей)

### Описание
Группировка полей с рамкой и легендой (альтернатива group).

### Параметры
```php
[
    'type'   => 'fieldset',
    'label'  => 'Contact Information',
    'fields' => [
        ['id' => 'name', 'type' => 'text', 'label' => 'Name'],
        ['id' => 'email', 'type' => 'email', 'label' => 'Email'],
    ],
]
```

### Реализация

#### PHP
```php
// В init_field_types()
self::$field_types['fieldset'] = ['render_fieldset', []];

// Метод рендера
private function render_fieldset(): string
{
    $field = $this->field;
    $fields = $field['fields'] ?? [];
    $label = $field['label'] ?? '';
    
    $html = '<fieldset class="wp-field-fieldset">';
    
    if ($label) {
        $html .= sprintf('<legend>%s</legend>', esc_html($label));
    }
    
    foreach ($fields as $sub_field) {
        $sub_field['id'] = $field['id'] . '_' . $sub_field['id'];
        $html .= self::make($sub_field, false, $this->storage_type, $this->storage_id);
    }
    
    $html .= '</fieldset>';
    
    return $html;
}
```

#### CSS
```css
.wp-field-fieldset {
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin: 10px 0;
}

.wp-field-fieldset legend {
    padding: 0 10px;
    font-weight: 600;
    color: #333;
}

.wp-field-fieldset .wp-field {
    margin-bottom: 15px;
}

.wp-field-fieldset .wp-field:last-child {
    margin-bottom: 0;
}
```

---

## Порядок реализации

### Этап 1 (1 час) — Информационные поля
1. **Heading** (15 мин)
2. **Subheading** (15 мин)
3. **Notice** (15 мин)
4. **Content** (15 мин)

### Этап 2 (1 час) — Улучшенные input
5. **Switcher** (30 мин)
6. **Spinner** (30 мин)

### Этап 3 (2 часа) — Кастомные UI
7. **Button Set** (1 час)
8. **Slider** (1 час)

### Этап 4 (30 мин) — Группировка
9. **Fieldset** (30 мин)

---

## Изменения в файлах

### 1. WP_Field.php
- Добавить 9 новых типов в `init_field_types()`
- Добавить 9 методов `render_**()`

### 2. assets/css/wp-field.css
- Добавить стили для каждого типа

### 3. assets/js/wp-field.js
- Добавить инициализацию для Spinner, Button Set, Slider
- Добавить обработчики событий

### 4. tests/
- Добавить тесты для каждого типа

---

## Тестирование

Создать тестовую страницу с примерами всех полей:

```php
WP_Field::make(['type' => 'switcher', 'id' => 'test_switcher', 'label' => 'Switcher']);
WP_Field::make(['type' => 'spinner', 'id' => 'test_spinner', 'label' => 'Spinner']);
WP_Field::make(['type' => 'button_set', 'id' => 'test_buttons', 'label' => 'Buttons', 'options' => ['a'=>'A','b'=>'B']]);
WP_Field::make(['type' => 'slider', 'id' => 'test_slider', 'label' => 'Slider']);
WP_Field::make(['type' => 'heading', 'content' => 'Section Title']);
WP_Field::make(['type' => 'subheading', 'content' => 'Subsection']);
WP_Field::make(['type' => 'notice', 'style' => 'info', 'content' => 'Information']);
WP_Field::make(['type' => 'content', 'content' => '<p>Custom content</p>']);
WP_Field::make(['type' => 'fieldset', 'label' => 'Group', 'fields' => [...]]);
```

---

## Итого

- **9 новых типов**
- **~400 строк PHP**
- **~300 строк CSS**
- **~100 строк JS**
- **Время: ~5 часов**
- **Результат: 34 типа полей (62% покрытия)**

---

## Следующие шаги

После реализации:
1. Протестировать все типы
2. Добавить Pest тесты
3. Обновить документацию
4. Создать примеры использования
5. Коммит с тегом v2.1.0
