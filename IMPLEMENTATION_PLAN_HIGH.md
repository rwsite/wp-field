# План реализации полей высокой сложности

## Обзор

8 типов полей высокой сложности (~18.5 часов):
1. Code Editor (3 часа) — CodeMirror/Ace
2. Icon (2 часа) — Font Awesome/Dashicons
3. Map (3 часа) — Google Maps API
4. Sortable (2 часа) — jQuery UI Sortable
5. Sorter (2 часа) — jQuery UI Sortable
6. Palette (2 часа) — Custom
7. Link (1.5 часа) — Custom
8. Backup (3 часа) — Custom

---

## 1. Code Editor (редактор кода)

### Описание
Редактор кода с подсветкой синтаксиса.

### Зависимости
- CodeMirror (встроен в WordPress) или Ace Editor

### Параметры
```php
[
    'id'       => 'custom_css',
    'type'     => 'code_editor',
    'label'    => 'Custom CSS',
    'mode'     => 'css', // css, javascript, php, html
    'theme'    => 'default',
    'height'   => '300px',
]
```

### Реализация
```php
// Использовать wp_enqueue_code_editor()
private function render_code_editor(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $mode = $field['mode'] ?? 'css';
    
    wp_enqueue_code_editor(['type' => $mode]);
    
    $html = sprintf(
        '<textarea id="%s" name="%s" class="wp-field-code-editor" data-mode="%s">%s</textarea>',
        esc_attr($field['id']),
        esc_attr($field['name'] ?? $field['id']),
        esc_attr($mode),
        esc_textarea($value)
    );
    
    return $html;
}
```

### JS
```javascript
initCodeEditor: function() {
    $('.wp-field-code-editor').each(function() {
        const mode = $(this).data('mode');
        wp.codeEditor.initialize(this, {
            type: mode,
        });
    });
}
```

---

## 2. Icon (выбор иконки)

### Описание
Выбор иконки из библиотеки (Dashicons, Font Awesome).

### Параметры
```php
[
    'id'      => 'menu_icon',
    'type'    => 'icon',
    'label'   => 'Menu Icon',
    'library' => 'dashicons', // dashicons или fontawesome
]
```

### Реализация
```php
private function render_icon(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $library = $field['library'] ?? 'dashicons';
    
    $icons = $this->get_icon_library($library);
    
    $html = '<div class="wp-field-icon-picker">';
    $html .= sprintf(
        '<input type="hidden" name="%s" value="%s" class="wp-field-icon-value" />',
        esc_attr($field['name'] ?? $field['id']),
        esc_attr($value)
    );
    
    $html .= '<button type="button" class="button wp-field-icon-button">';
    if ($value) {
        $html .= sprintf('<span class="%s %s"></span>', $library, esc_attr($value));
    } else {
        $html .= 'Select Icon';
    }
    $html .= '</button>';
    
    // Modal с иконками
    $html .= '<div class="wp-field-icon-modal" style="display:none;">';
    $html .= '<div class="wp-field-icon-grid">';
    foreach ($icons as $icon) {
        $html .= sprintf(
            '<span class="%s %s" data-icon="%s"></span>',
            $library,
            esc_attr($icon),
            esc_attr($icon)
        );
    }
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}

private function get_icon_library(string $library): array
{
    if ($library === 'dashicons') {
        return [
            'dashicons-admin-site',
            'dashicons-dashboard',
            'dashicons-admin-post',
            // ... все dashicons
        ];
    }
    
    return [];
}
```

### CSS
```css
.wp-field-icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
    gap: 5px;
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
}

.wp-field-icon-grid span {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.wp-field-icon-grid span:hover {
    background: #0073aa;
    color: white;
}
```

---

## 3. Map (карта)

### Описание
Выбор координат на карте Google Maps.

### Зависимости
- Google Maps API

### Параметры
```php
[
    'id'     => 'location',
    'type'   => 'map',
    'label'  => 'Location',
    'api_key' => 'YOUR_GOOGLE_MAPS_API_KEY',
    'zoom'   => 12,
    'center' => ['lat' => 55.7558, 'lng' => 37.6173], // Москва
]
```

### Реализация
```php
private function render_map(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $value = is_array($value) ? $value : ['lat' => '', 'lng' => ''];
    
    $api_key = $field['api_key'] ?? '';
    
    if (empty($api_key)) {
        return '<p class="description">Google Maps API key required</p>';
    }
    
    // Enqueue Google Maps
    wp_enqueue_script(
        'google-maps',
        'https://maps.googleapis.com/maps/api/js?key=' . $api_key,
        [],
        null,
        true
    );
    
    $html = '<div class="wp-field-map-wrapper">';
    $html .= sprintf(
        '<input type="hidden" name="%s[lat]" value="%s" class="wp-field-map-lat" />',
        esc_attr($field['id']),
        esc_attr($value['lat'])
    );
    $html .= sprintf(
        '<input type="hidden" name="%s[lng]" value="%s" class="wp-field-map-lng" />',
        esc_attr($field['id']),
        esc_attr($value['lng'])
    );
    $html .= '<div class="wp-field-map" style="height:400px;"></div>';
    $html .= '</div>';
    
    return $html;
}
```

### JS
```javascript
initMap: function() {
    $('.wp-field-map').each(function() {
        const $wrapper = $(this).closest('.wp-field-map-wrapper');
        const $lat = $wrapper.find('.wp-field-map-lat');
        const $lng = $wrapper.find('.wp-field-map-lng');
        
        const lat = parseFloat($lat.val()) || 55.7558;
        const lng = parseFloat($lng.val()) || 37.6173;
        
        const map = new google.maps.Map(this, {
            center: {lat: lat, lng: lng},
            zoom: 12
        });
        
        const marker = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map,
            draggable: true
        });
        
        marker.addListener('dragend', function() {
            const pos = marker.getPosition();
            $lat.val(pos.lat());
            $lng.val(pos.lng());
        });
    });
}
```

---

## 4. Sortable (сортируемый список)

### Описание
Список элементов с возможностью сортировки.

### Зависимости
- jQuery UI Sortable (встроен в WordPress)

### Параметры
```php
[
    'id'      => 'menu_order',
    'type'    => 'sortable',
    'label'   => 'Menu Order',
    'options' => [
        'home'     => 'Home',
        'about'    => 'About',
        'services' => 'Services',
        'contact'  => 'Contact',
    ],
]
```

### Реализация
```php
private function render_sortable(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $value = is_array($value) ? $value : [];
    $options = $field['options'] ?? [];
    
    // Сортируем опции по сохранённому порядку
    $sorted = [];
    foreach ($value as $key) {
        if (isset($options[$key])) {
            $sorted[$key] = $options[$key];
        }
    }
    
    // Добавляем оставшиеся
    foreach ($options as $key => $label) {
        if (!isset($sorted[$key])) {
            $sorted[$key] = $label;
        }
    }
    
    $html = '<ul class="wp-field-sortable">';
    foreach ($sorted as $key => $label) {
        $html .= sprintf(
            '<li data-value="%s">
                <span class="dashicons dashicons-menu"></span>
                <span>%s</span>
                <input type="hidden" name="%s[]" value="%s" />
            </li>',
            esc_attr($key),
            esc_html($label),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($key)
        );
    }
    $html .= '</ul>';
    
    return $html;
}
```

### CSS
```css
.wp-field-sortable {
    list-style: none;
    margin: 0;
    padding: 0;
}

.wp-field-sortable li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    margin-bottom: 5px;
    cursor: move;
}

.wp-field-sortable li:hover {
    background: #f0f0f0;
}

.wp-field-sortable .dashicons {
    color: #999;
}
```

### JS
```javascript
initSortable: function() {
    if (typeof $.fn.sortable !== 'undefined') {
        $('.wp-field-sortable').sortable({
            handle: '.dashicons-menu',
            placeholder: 'wp-field-sortable-placeholder'
        });
    }
}
```

---

## 5. Sorter (сортировка между группами)

### Описание
Сортировка элементов между двумя группами (enabled/disabled).

### Параметры
```php
[
    'id'      => 'widgets',
    'type'    => 'sorter',
    'label'   => 'Widgets',
    'enabled' => ['widget1', 'widget2'],
    'disabled' => ['widget3', 'widget4'],
    'options' => [
        'widget1' => 'Widget 1',
        'widget2' => 'Widget 2',
        'widget3' => 'Widget 3',
        'widget4' => 'Widget 4',
    ],
]
```

### Реализация
```php
private function render_sorter(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $value = is_array($value) ? $value : ['enabled' => [], 'disabled' => []];
    $options = $field['options'] ?? [];
    
    $html = '<div class="wp-field-sorter">';
    
    // Enabled
    $html .= '<div class="wp-field-sorter-group">';
    $html .= '<h4>Enabled</h4>';
    $html .= '<ul class="wp-field-sorter-list" data-group="enabled">';
    foreach ($value['enabled'] ?? [] as $key) {
        if (isset($options[$key])) {
            $html .= sprintf(
                '<li data-value="%s">
                    <span>%s</span>
                    <input type="hidden" name="%s[enabled][]" value="%s" />
                </li>',
                esc_attr($key),
                esc_html($options[$key]),
                esc_attr($field['id']),
                esc_attr($key)
            );
        }
    }
    $html .= '</ul>';
    $html .= '</div>';
    
    // Disabled
    $html .= '<div class="wp-field-sorter-group">';
    $html .= '<h4>Disabled</h4>';
    $html .= '<ul class="wp-field-sorter-list" data-group="disabled">';
    foreach ($value['disabled'] ?? [] as $key) {
        if (isset($options[$key])) {
            $html .= sprintf(
                '<li data-value="%s">
                    <span>%s</span>
                    <input type="hidden" name="%s[disabled][]" value="%s" />
                </li>',
                esc_attr($key),
                esc_html($options[$key]),
                esc_attr($field['id']),
                esc_attr($key)
            );
        }
    }
    $html .= '</ul>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}
```

### JS
```javascript
initSorter: function() {
    if (typeof $.fn.sortable !== 'undefined') {
        $('.wp-field-sorter-list').sortable({
            connectWith: '.wp-field-sorter-list',
            placeholder: 'wp-field-sorter-placeholder'
        });
    }
}
```

---

## 6. Palette (палитра цветов)

### Описание
Выбор из предустановленной палитры цветов.

### Параметры
```php
[
    'id'      => 'theme_color',
    'type'    => 'palette',
    'label'   => 'Theme Color',
    'palettes' => [
        'blue'   => ['#0073aa', '#005a87', '#004066'],
        'green'  => ['#46b450', '#3a9940', '#2e7d32'],
        'red'    => ['#dc3232', '#c62828', '#b71c1c'],
    ],
]
```

### Реализация
```php
private function render_palette(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $palettes = $field['palettes'] ?? [];
    
    $html = '<div class="wp-field-palette">';
    
    foreach ($palettes as $key => $colors) {
        $checked = $value === $key ? 'checked' : '';
        $html .= sprintf(
            '<label class="wp-field-palette-item %s">
                <input type="radio" name="%s" value="%s" %s />
                <span class="wp-field-palette-colors">',
            $checked ? 'active' : '',
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($key),
            $checked
        );
        
        foreach ($colors as $color) {
            $html .= sprintf(
                '<span class="wp-field-palette-color" style="background-color:%s;"></span>',
                esc_attr($color)
            );
        }
        
        $html .= '</span></label>';
    }
    
    $html .= '</div>';
    
    return $html;
}
```

### CSS
```css
.wp-field-palette {
    display: flex;
    gap: 10px;
}

.wp-field-palette-item {
    cursor: pointer;
}

.wp-field-palette-item input {
    display: none;
}

.wp-field-palette-colors {
    display: flex;
    gap: 3px;
    padding: 5px;
    border: 2px solid transparent;
    border-radius: 4px;
}

.wp-field-palette-item.active .wp-field-palette-colors {
    border-color: #0073aa;
}

.wp-field-palette-color {
    width: 30px;
    height: 30px;
    border-radius: 3px;
}
```

---

## 7. Link (выбор ссылки)

### Описание
Выбор ссылки с настройками (URL, текст, target).

### Параметры
```php
[
    'id'    => 'button_link',
    'type'  => 'link',
    'label' => 'Button Link',
]
```

### Реализация
```php
private function render_link(): string
{
    $field = $this->field;
    $value = $this->get_field_value($field);
    $value = is_array($value) ? $value : ['url' => '', 'text' => '', 'target' => '_self'];
    
    $html = '<div class="wp-field-link">';
    
    // URL
    $html .= '<div class="wp-field-link-item">';
    $html .= '<label>URL</label>';
    $html .= sprintf(
        '<input type="url" name="%s[url]" value="%s" placeholder="https://" />',
        esc_attr($field['id']),
        esc_attr($value['url'])
    );
    $html .= '</div>';
    
    // Text
    $html .= '<div class="wp-field-link-item">';
    $html .= '<label>Link Text</label>';
    $html .= sprintf(
        '<input type="text" name="%s[text]" value="%s" />',
        esc_attr($field['id']),
        esc_attr($value['text'])
    );
    $html .= '</div>';
    
    // Target
    $html .= '<div class="wp-field-link-item">';
    $html .= '<label>';
    $html .= sprintf(
        '<input type="checkbox" name="%s[target]" value="_blank" %s /> Open in new tab',
        esc_attr($field['id']),
        $value['target'] === '_blank' ? 'checked' : ''
    );
    $html .= '</label>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}
```

---

## 8. Backup (резервная копия)

### Описание
Экспорт/импорт настроек.

### Параметры
```php
[
    'type'  => 'backup',
    'label' => 'Backup Settings',
]
```

### Реализация
```php
private function render_backup(): string
{
    $html = '<div class="wp-field-backup">';
    
    // Export
    $html .= '<div class="wp-field-backup-section">';
    $html .= '<h4>Export Settings</h4>';
    $html .= '<button type="button" class="button wp-field-backup-export">Export</button>';
    $html .= '</div>';
    
    // Import
    $html .= '<div class="wp-field-backup-section">';
    $html .= '<h4>Import Settings</h4>';
    $html .= '<textarea class="wp-field-backup-import-data" placeholder="Paste exported data here"></textarea>';
    $html .= '<button type="button" class="button wp-field-backup-import">Import</button>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}
```

### JS
```javascript
initBackup: function() {
    // Export
    $(document).on('click', '.wp-field-backup-export', function() {
        const data = $('form').serialize();
        const json = JSON.stringify(data);
        
        // Создаём файл для скачивания
        const blob = new Blob([json], {type: 'application/json'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'settings-backup.json';
        a.click();
    });
    
    // Import
    $(document).on('click', '.wp-field-backup-import', function() {
        const data = $('.wp-field-backup-import-data').val();
        
        try {
            const json = JSON.parse(data);
            // Заполняем форму
            // ...
            alert('Settings imported successfully');
        } catch (e) {
            alert('Invalid backup data');
        }
    });
}
```

---

## Итого

- **8 новых типов**
- **~600 строк PHP**
- **~400 строк CSS**
- **~400 строк JS**
- **Время: ~18.5 часов**
- **Результат: 52 типа полей (95% покрытия)**

## Зависимости

- CodeMirror (встроен в WP)
- jQuery UI Sortable (встроен в WP)
- Google Maps API (требует ключ)
- Dashicons (встроен в WP)
