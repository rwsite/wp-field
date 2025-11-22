<?php
/**
 * Plugin Name: Universal html field generator for WP
 * Plugin URI:  https://rwsite.ru
 * Description: WP library plugin. Universal HTML field generator for WordPress. See usage examples in readme.md
 * Version:     1.1.0
 * Author:      Aleksei Tikhomirov
 * Author URI:  https://rwsite.ru
 * Text Domain: wp-field
 * Domain Path: /lang/
 *
 * Requires at least: 4.6
 * Requires PHP: 7.4
 *
 * How to use: WP_Field::make(['id' => 'new_id', 'label' => 'New input']);
 * @package wp-field
 */

if (!defined('ABSPATH') || class_exists('WP_Field')) {
    return;
}

#[AllowDynamicProperties]
class WP_Field
{
    public const file = __FILE__;

    /** Типы хранилищ значений */
    public static array $allowed_storage_types = [
        'post',     // post meta
        'options',  // option
        'term',     // term meta
        'user',     // user meta
        'comment',  // comment meta
    ];

    /** Реестр типов полей: type => [renderer_method, defaults] */
    private static array $field_types = [];

    /** Флаг однократной инициализации assets */
    private static bool $assets_enqueued = false;

    /**
     *
     * @var $field array{ id: string, type: string, label: string, class: string, custom_attributes: array }
     *     'id': meta-key|option name
     *     'options': array - for select
     *     'parse_options' - parse text options as: option:value
     *     'desc'
     */
    public $field;

    /** @var string|null - тип хранилища (post|options|term|user|comment) */
    public $storage_type;

    /** @var int|string|null - ID объекта (post|term|user|comment) для получения значения */
    public $storage_id;

    public string $file;
    public string $url;
    public string $ver = '1.1.0';

    /**
     * Инициализация реестра типов полей (однократно)
     */
    public static function init_field_types(): void
    {
        if (!empty(self::$field_types)) {
            return;
        }

        // Базовые поля
        self::$field_types['text'] = ['render_text', ['type' => 'text']];
        self::$field_types['password'] = ['render_text', ['type' => 'password']];
        self::$field_types['email'] = ['render_text', ['type' => 'email']];
        self::$field_types['url'] = ['render_text', ['type' => 'url']];
        self::$field_types['tel'] = ['render_text', ['type' => 'tel']];
        self::$field_types['number'] = ['render_number', ['type' => 'number']];
        self::$field_types['range'] = ['render_text', ['type' => 'range']];
        self::$field_types['hidden'] = ['render_text', ['type' => 'hidden']];
        self::$field_types['textarea'] = ['render_textarea', []];

        // Выбор
        self::$field_types['select'] = ['render_select', []];
        self::$field_types['multiselect'] = ['render_select', ['multiple' => true]];
        self::$field_types['radio'] = ['render_radio', []];
        self::$field_types['checkbox'] = ['render_checkbox', []];
        self::$field_types['checkbox_group'] = ['render_checkbox_group', []];

        // Продвинутые
        self::$field_types['editor'] = ['render_editor', []];
        self::$field_types['media'] = ['render_media', []];
        self::$field_types['image'] = ['render_image', []];
        self::$field_types['file'] = ['render_file', []];
        self::$field_types['gallery'] = ['render_gallery', []];
        self::$field_types['color'] = ['render_color', []];
        self::$field_types['date'] = ['render_date', ['type' => 'date']];
        self::$field_types['time'] = ['render_date', ['type' => 'time']];
        self::$field_types['datetime'] = ['render_date', ['type' => 'datetime-local']];

        // Композитные
        self::$field_types['group'] = ['render_group', []];
        self::$field_types['repeater'] = ['render_repeater', []];

        // Алиасы для обратной совместимости
        self::$field_types['date_time'] = self::$field_types['datetime'];
        self::$field_types['datetime-local'] = self::$field_types['datetime'];
        self::$field_types['image_picker'] = ['render_image_picker', []];
        self::$field_types['imagepicker'] = self::$field_types['image_picker'];
    }

    public function __construct($field, string $storage_type = 'post', $storage_id = null)
    {
        self::init_field_types();

        $this->storage_type = $storage_type;
        $this->storage_id = $storage_id;

        if (!isset($this->storage_id) && $storage_type === 'post') {
            $this->storage_id = get_the_ID();
        }

        // Нормализация алиасов полей
        $field['value'] = $field['value'] ?? $field['val'] ?? null;
        $field['label'] = $field['title'] ?? $field['label'] ?? null;
        $field['custom_attributes'] = $field['custom_attributes'] ?? $field['attributes'] ?? $field['attr'] ?? $field['atts'] ?? null;

        $this->field = $this->validate_field_data($field);

        $this->file = self::file;
        $this->url = plugin_dir_url($this->file);
        $this->ver = defined('WP_DEBUG') && WP_DEBUG ? time() : $this->ver;

        // Однократная инициализация assets
        $this->maybe_enqueue_assets();
    }

    /**
     * Validate field data before rendering
     */
    private function validate_field_data($field)
    {
        if (is_object($field)) {
            $field = get_object_vars($field);
        }

        if (is_string($field)) {
            $str = $field;
            $field = [];
            parse_str($str, $field);
        }

        if (empty($field) || !isset($field['id'], $field['type'], $field['label']) || !is_array($field)) {
            trigger_error('!!! Incorrect field data ' . print_r($field, true));
            return 'Incorrect field data';
        }

        return $field;
    }

    /**
     * Make new filed
     *
     * @param array $params data, type, id,
     * @param bool $output html|string
     *
     * @return false|string|null
     */
    public static function make(array $params, bool $output = true)
    {
        if (isset($params[0]) && is_array($params[0])) {
            $obj = new self($params[0], $params[1]??'post', $params[2]??null);
        } else {
            $obj = new self($params);
        }
        return $obj->render($output);
    }

    /**
     * Рендер поля с wrapper'ом
     *
     * @param bool $output выводить HTML или вернуть строку
     * @return false|string|void
     */
    public function render($output = true)
    {
        if (!$output) {
            ob_start();
        }

        // Определяем видимость по зависимостям
        $is_hidden = $this->is_field_hidden();
        $hidden_class = $is_hidden ? ' is-hidden' : '';
        $dependency_data = $this->get_dependency_data();

        // Открываем wrapper
        printf(
            '<div class="wp-field wp-field-%s%s" data-field-id="%s" data-field-type="%s"%s>',
            esc_attr($this->field['type']),
            $hidden_class,
            esc_attr($this->field['id']),
            esc_attr($this->field['type']),
            $dependency_data ? ' data-dependency=\'' . esc_attr($dependency_data) . '\'' : ''
        );

        // Рендерим поле через реестр
        $this->render_field();

        // Закрываем wrapper
        echo '</div>';

        if (!$output) {
            return ob_get_clean();
        }
    }

    /**
     * Рендер поля по типу из реестра
     */
    private function render_field(): void
    {
        $type = $this->field['type'] ?? 'text';

        if (!isset(self::$field_types[$type])) {
            trigger_error("Неизвестный тип поля: {$type}");
            return;
        }

        [$method, $defaults] = self::$field_types[$type];

        // Мержим дефолты с полем
        $field = array_merge($defaults, $this->field);

        // Вызываем метод рендера
        if (method_exists($this, $method)) {
            $this->$method($field);
        } else {
            trigger_error("Метод рендера не найден: {$method}");
        }
    }

    /**
     * Проверка, скрыто ли поле по зависимостям
     */
    private function is_field_hidden(): bool
    {
        if (empty($this->field['dependency'])) {
            return false;
        }

        return !$this->evaluate_dependency($this->field['dependency']);
    }

    /**
     * Оценка условия зависимости
     */
    private function evaluate_dependency(array $dependency): bool
    {
        $relation = $dependency['relation'] ?? 'AND';
        $conditions = array_filter($dependency, fn($k) => $k !== 'relation', ARRAY_FILTER_USE_KEY);

        if (empty($conditions)) {
            return true;
        }

        $results = [];
        foreach ($conditions as $condition) {
            if (!is_array($condition) || count($condition) < 3) {
                continue;
            }

            [$field_id, $operator, $value] = $condition;
            $field_value = $this->get_value_for_dependency($field_id);
            $results[] = $this->evaluate_condition($field_value, $operator, $value);
        }

        if (empty($results)) {
            return true;
        }

        return 'AND' === $relation ? !in_array(false, $results, true) : in_array(true, $results, true);
    }

    /**
     * Получить значение поля для проверки зависимости
     */
    private function get_value_for_dependency(string $field_id)
    {
        return $this->get_value($field_id, $this->storage_id);
    }

    /**
     * Оценить одно условие
     */
    private function evaluate_condition($field_value, string $operator, $compare_value): bool
    {
        return match ($operator) {
            '==' => $field_value == $compare_value,
            '!=' => $field_value != $compare_value,
            '>' => $field_value > $compare_value,
            '>=' => $field_value >= $compare_value,
            '<' => $field_value < $compare_value,
            '<=' => $field_value <= $compare_value,
            'in' => is_array($compare_value) && in_array($field_value, $compare_value, true),
            'not_in' => is_array($compare_value) && !in_array($field_value, $compare_value, true),
            'contains' => is_string($field_value) && str_contains($field_value, (string)$compare_value),
            'not_contains' => is_string($field_value) && !str_contains($field_value, (string)$compare_value),
            'empty' => empty($field_value),
            'not_empty' => !empty($field_value),
            default => false,
        };
    }

    /**
     * Получить JSON данные зависимостей для JS
     */
    private function get_dependency_data(): string
    {
        if (empty($this->field['dependency'])) {
            return '';
        }

        return wp_json_encode($this->field['dependency']);
    }

    /**
     * Однократная инициализация assets
     */
    private function maybe_enqueue_assets(): void
    {
        if (self::$assets_enqueued || !is_admin()) {
            return;
        }

        self::$assets_enqueued = true;

        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Подключение assets (JS/CSS)
     */
    public function enqueue_assets(): void
    {
        // WP встроенные скрипты
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui');

        // wp-color-picker для color полей
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');

        // wp-media для media полей
        wp_enqueue_media();

        // Наш JS для зависимостей и инициализации
        wp_enqueue_script(
            'wp-field-main',
            $this->url . 'assets/js/wp-field.js',
            ['jquery'],
            $this->ver,
            true
        );

        // Наш CSS
        wp_enqueue_style(
            'wp-field-main',
            $this->url . 'assets/css/wp-field.css',
            [],
            $this->ver
        );

        // Локализация
        wp_localize_script('wp-field-main', 'wpFieldConfig', [
            'nonce' => wp_create_nonce('wp_field_nonce'),
        ]);
    }

    /**
     * Рендер базового текстового поля (text, password, email, url, tel, range, hidden)
     */
    private function render_text(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input class="%s" id="%s" name="%s" type="%s" value="%s" placeholder="%s" %s %s>',
            esc_attr($field['class'] ?? 'regular-text'),
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($field['type'] ?? 'text'),
            esc_attr($this->get_field_value($field)),
            esc_attr($field['placeholder'] ?? ''),
            $this->get_field_attributes($field),
            $this->get_readonly_disabled($field)
        );

        $this->render_description($field);
    }

    /**
     * Рендер числового поля
     */
    private function render_number(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input class="%s" id="%s" name="%s" type="number" value="%s" %s %s %s %s>',
            esc_attr($field['class'] ?? 'regular-text'),
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($this->get_field_value($field)),
            isset($field['min']) ? 'min="' . esc_attr($field['min']) . '"' : '',
            isset($field['max']) ? 'max="' . esc_attr($field['max']) . '"' : '',
            isset($field['step']) ? 'step="' . esc_attr($field['step']) . '"' : '',
            $this->get_readonly_disabled($field)
        );

        $this->render_description($field);
    }

    /**
     * Рендер textarea
     */
    private function render_textarea(array $field): void
    {
        $this->render_label($field);

        printf(
            '<textarea class="%s" id="%s" name="%s" rows="%d" %s>%s</textarea>',
            esc_attr($field['class'] ?? 'regular-text'),
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            absint($field['rows'] ?? 5),
            $this->get_readonly_disabled($field),
            esc_textarea($this->get_field_value($field))
        );

        $this->render_description($field);
    }

    /**
     * Рендер select
     */
    private function render_select(array $field): void
    {
        $this->render_label($field);

        $multiple = !empty($field['multiple']) ? ' multiple="multiple"' : '';

        printf(
            '<select class="%s" id="%s" name="%s"%s %s>%s</select>',
            esc_attr($field['class'] ?? ''),
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            $multiple,
            $this->get_field_attributes($field),
            $this->render_select_options($field)
        );

        $this->render_description($field);
    }

    /**
     * Рендер опций select
     */
    private function render_select_options(array $field): string
    {
        $options = $field['options'] ?? [];
        $value = $this->get_field_value($field);

        if (is_string($options)) {
            $options = array_filter(array_map('trim', explode("\n", $options)));
        }

        $output = [];

        foreach ($options as $opt_value => $opt_label) {
            // Поддержка parse_options: "label:value"
            if (!empty($field['parse_options']) && is_string($opt_label) && str_contains($opt_label, ':')) {
                [$opt_label, $opt_value] = array_map('trim', explode(':', $opt_label, 2));
            }

            $selected = $this->is_value_selected($value, $opt_value) ? ' selected' : '';

            $output[] = sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr($opt_value),
                $selected,
                esc_html($opt_label)
            );
        }

        return implode('', $output);
    }

    /**
     * Рендер radio
     */
    private function render_radio(array $field): void
    {
        $this->render_label($field);

        $options = $field['options'] ?? [];
        $value = $this->get_field_value($field);

        if (is_string($options)) {
            $options = array_filter(array_map('trim', explode("\n", $options)));
        }

        $output = [];
        $i = 0;

        foreach ($options as $opt_value => $opt_label) {
            $checked = $this->is_value_selected($value, $opt_value) ? ' checked' : '';

            $output[] = sprintf(
                '<label><input type="radio" name="%s" value="%s" id="%s-%d"%s> %s</label>',
                esc_attr($field['name'] ?? $field['id']),
                esc_attr($opt_value),
                esc_attr($field['id']),
                $i,
                $checked,
                esc_html($opt_label)
            );

            $i++;
        }

        echo '<div class="wp-field-radio-group">' . implode('', $output) . '</div>';

        $this->render_description($field);
    }

    /**
     * Рендер одиночного checkbox
     */
    private function render_checkbox(array $field): void
    {
        $value = $this->get_field_value($field);
        $checked = $this->is_checked($value) ? ' checked' : '';

        printf(
            '<input type="checkbox" id="%s" name="%s" value="1"%s %s>',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            $checked,
            $this->get_field_attributes($field)
        );

        $this->render_label($field);
        $this->render_description($field);
    }

    /**
     * Рендер группы checkbox'ов
     */
    private function render_checkbox_group(array $field): void
    {
        $this->render_label($field);

        $options = $field['options'] ?? [];
        $values = (array)$this->get_field_value($field);

        if (is_string($options)) {
            $options = array_filter(array_map('trim', explode("\n", $options)));
        }

        $output = [];
        $i = 0;

        foreach ($options as $opt_value => $opt_label) {
            $checked = in_array($opt_value, $values, true) ? ' checked' : '';

            $output[] = sprintf(
                '<label><input type="checkbox" name="%s[]" value="%s" id="%s-%d"%s> %s</label>',
                esc_attr($field['name'] ?? $field['id']),
                esc_attr($opt_value),
                esc_attr($field['id']),
                $i,
                $checked,
                esc_html($opt_label)
            );

            $i++;
        }

        echo '<div class="wp-field-checkbox-group">' . implode('', $output) . '</div>';

        $this->render_description($field);
    }

    /**
     * Рендер wp_editor
     */
    private function render_editor(array $field): void
    {
        $this->render_label($field);

        if (function_exists('wp_editor')) {
            wp_editor($this->get_field_value($field), $field['id'], [
                'wpautop' => !empty($field['wpautop']),
                'media_buttons' => !empty($field['media_buttons']),
                'textarea_name' => $field['name'] ?? $field['id'],
                'textarea_rows' => absint($field['rows'] ?? 10),
                'teeny' => !empty($field['teeny']),
            ]);
        } else {
            trigger_error('wp_editor не найден');
        }

        $this->render_description($field);
    }

    /**
     * Рендер media (ID или URL)
     */
    private function render_media(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="wp-field-media-id">',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($this->get_field_value($field))
        );

        printf(
            '<button type="button" class="button wp-field-media-button" data-field-id="%s">%s</button>',
            esc_attr($field['id']),
            esc_html($field['button_text'] ?? __('Choose Media', 'wp-field'))
        );

        $this->render_description($field);
    }

    /**
     * Рендер image
     */
    private function render_image(array $field): void
    {
        $this->render_label($field);

        $value = $this->get_field_value($field);
        $src = is_numeric($value) ? wp_get_attachment_url($value) : $value;

        if (!$src) {
            $src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIj48cmVjdCBmaWxsPSIjZTBlMGUwIiB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIvPjwvc3ZnPg==';
        }

        printf(
            '<img src="%s" alt="%s" class="wp-field-image-preview" style="max-width: 200px; height: auto; margin-bottom: 10px;">',
            esc_url($src),
            esc_attr($field['label'] ?? '')
        );

        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="wp-field-image-id">',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($value)
        );

        printf(
            '<button type="button" class="button wp-field-image-button" data-field-id="%s">%s</button>
            <button type="button" class="button wp-field-image-remove" data-field-id="%s">%s</button>',
            esc_attr($field['id']),
            esc_html($field['button_text'] ?? __('Choose Image', 'wp-field')),
            esc_attr($field['id']),
            esc_html(__('Remove', 'wp-field'))
        );

        $this->render_description($field);
    }

    /**
     * Рендер file
     */
    private function render_file(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="wp-field-file-id">',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($this->get_field_value($field))
        );

        printf(
            '<button type="button" class="button wp-field-file-button" data-field-id="%s">%s</button>',
            esc_attr($field['id']),
            esc_html($field['button_text'] ?? __('Choose File', 'wp-field'))
        );

        $this->render_description($field);
    }

    /**
     * Рендер gallery
     */
    private function render_gallery(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="wp-field-gallery-ids">',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr(is_array($this->get_field_value($field)) ? implode(',', $this->get_field_value($field)) : $this->get_field_value($field))
        );

        printf(
            '<button type="button" class="button wp-field-gallery-button" data-field-id="%s">%s</button>',
            esc_attr($field['id']),
            esc_html($field['button_text'] ?? __('Choose Gallery', 'wp-field'))
        );

        $this->render_description($field);
    }

    /**
     * Рендер color picker
     */
    private function render_color(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="wp-color-picker-field" %s>',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($this->get_field_value($field)),
            $this->get_field_attributes($field)
        );

        $this->render_description($field);
    }

    /**
     * Рендер date/time/datetime
     */
    private function render_date(array $field): void
    {
        $this->render_label($field);

        printf(
            '<input class="%s" id="%s" name="%s" type="%s" value="%s" %s>',
            esc_attr($field['class'] ?? 'regular-text'),
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id']),
            esc_attr($field['type'] ?? 'date'),
            esc_attr($this->get_field_value($field)),
            $this->get_field_attributes($field)
        );

        $this->render_description($field);
    }

    /**
     * Рендер group (вложенные поля)
     */
    private function render_group(array $field): void
    {
        $this->render_label($field);

        if (empty($field['fields'])) {
            return;
        }

        echo '<div class="wp-field-group">';

        foreach ($field['fields'] as $sub_field) {
            $sub_field['name'] = $field['name'] ?? $field['id'];
            $obj = new self($sub_field, $this->storage_type, $this->storage_id);
            $obj->render(true);
        }

        echo '</div>';

        $this->render_description($field);
    }

    /**
     * Рендер repeater (массив элементов)
     */
    private function render_repeater(array $field): void
    {
        $this->render_label($field);

        $values = (array)$this->get_field_value($field);
        $min = absint($field['min'] ?? 0);
        $max = absint($field['max'] ?? 0);

        echo '<div class="wp-field-repeater" data-field-id="' . esc_attr($field['id']) . '" data-min="' . $min . '" data-max="' . $max . '">';

        foreach ($values as $index => $item) {
            $this->render_repeater_item($field, $index, $item);
        }

        // Добавляем пустой шаблон для добавления новых элементов
        if (empty($values) || ($max === 0 || count($values) < $max)) {
            $this->render_repeater_item($field, 0, [], true);
        }

        echo '</div>';

        printf(
            '<button type="button" class="button wp-field-repeater-add" data-field-id="%s">%s</button>',
            esc_attr($field['id']),
            esc_html($field['add_text'] ?? __('Add Item', 'wp-field'))
        );

        $this->render_description($field);
    }

    /**
     * Рендер одного элемента repeater
     */
    private function render_repeater_item(array $field, int $index, $item = [], bool $template = false): void
    {
        $class = $template ? ' wp-field-repeater-template' : '';

        printf(
            '<div class="wp-field-repeater-item%s" data-index="%d">',
            $class,
            $index
        );

        if (!empty($field['fields'])) {
            foreach ($field['fields'] as $sub_field) {
                $sub_field['name'] = $field['name'] ?? $field['id'];
                $sub_field['value'] = is_array($item) && isset($item[$sub_field['id']]) ? $item[$sub_field['id']] : null;

                $obj = new self($sub_field, $this->storage_type, $this->storage_id);
                $obj->render(true);
            }
        }

        printf(
            '<button type="button" class="button wp-field-repeater-remove">%s</button>',
            esc_html(__('Remove', 'wp-field'))
        );

        echo '</div>';
    }

    /**
     * Рендер image picker (legacy)
     */
    private function render_image_picker(array $field): void
    {
        // Используем select с data-img-src для каждой опции
        $this->render_label($field);

        $options = $field['options'] ?? [];
        $value = $this->get_field_value($field);

        printf(
            '<select class="wp-field-image-picker" id="%s" name="%s">',
            esc_attr($field['id']),
            esc_attr($field['name'] ?? $field['id'])
        );

        foreach ($options as $opt_value => $opt_data) {
            $selected = $value === $opt_value ? ' selected' : '';
            $img_src = is_array($opt_data) ? ($opt_data['src'] ?? '') : $opt_data;

            printf(
                '<option value="%s" data-img-src="%s"%s>%s</option>',
                esc_attr($opt_value),
                esc_url($img_src),
                $selected,
                esc_html(is_array($opt_data) ? ($opt_data['label'] ?? $opt_value) : $opt_value)
            );
        }

        echo '</select>';

        $this->render_description($field);
    }

    /**
     * Рендер label
     */
    private function render_label(array $field): void
    {
        if (!empty($field['label'])) {
            printf(
                '<label for="%s" class="%s">%s</label>',
                esc_attr($field['id']),
                esc_attr($field['label-class'] ?? 'input-label'),
                esc_html($field['label'])
            );
        }
    }

    /**
     * Рендер description
     */
    private function render_description(array $field): void
    {
        if (!empty($field['desc'])) {
            printf(
                '<p class="description">%s</p>',
                wp_kses_post($field['desc'])
            );
        }
    }

    /**
     * Получить значение поля
     */
    private function get_field_value(array $field)
    {
        $value = $field['value'] ?? null;

        // Если значение не задано, пытаемся получить из БД
        if ($value === null) {
            $value = $this->get_value($field['id'], $this->storage_id);
        }

        // Если всё ещё null, используем default
        if ($value === null) {
            $value = $field['default'] ?? '';
        }

        return $value;
    }

    /**
     * Проверить, выбрано ли значение
     */
    private function is_value_selected($current_value, $compare_value): bool
    {
        if (is_array($current_value)) {
            return in_array($compare_value, $current_value, true);
        }

        return $current_value == $compare_value;
    }

    /**
     * Проверить, отмечен ли checkbox
     */
    private function is_checked($value): bool
    {
        return $value === '1' || $value === 'on' || $value === 'yes' || $value === true;
    }

    /**
     * Получить HTML атрибутов поля
     */
    private function get_field_attributes(array $field): string
    {
        $attributes = [];

        if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {
            foreach ($field['custom_attributes'] as $attr => $val) {
                $attributes[] = esc_attr($attr) . '="' . esc_attr($val) . '"';
            }
        }

        return implode(' ', $attributes);
    }

    /**
     * Получить readonly/disabled атрибуты
     */
    private function get_readonly_disabled(array $field): string
    {
        $attrs = [];

        if (!empty($field['readonly'])) {
            $attrs[] = 'readonly';
        }

        if (!empty($field['disabled'])) {
            $attrs[] = 'disabled';
        }

        return implode(' ', $attrs);
    }

    /**
     * Получить значение поля из БД по типу хранилища
     *
     * @param string $key - option|meta name
     * @param int $id - object ID
     */
    public function get_value(string $key, $id = null)
    {
        $id = absint($id);

        switch ($this->storage_type) {
            case 'options':
                return get_option($key, null);
            case 'term':
                return get_term_meta($id, $key, true);
            case 'user':
                return get_user_meta($id, $key, true);
            case 'comment':
                return get_comment_meta($id, $key, true);
            case 'post':
            default:
                return get_post_meta($id ?: get_the_ID(), $key, true);
        }
    }
}
