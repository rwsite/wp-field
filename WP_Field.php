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
    // nav_menu_item
    public static array $allowed_types = [
        'post', // post meta
        'options',
        'term', // term meta
        'user',
        'comment',
    ];

    /**
     *
     * @var $field array{ id: string, type: string, label: string, class: string, custom_attributes: array }
     *     'id': meta-key|option name
     *     'options': array - for select
     *     'parse_options' - parse text options as: option:value
     *     'desc'
     */
    public $field;

    /** @var string|null - placement type (post meta|options|term meta|user|comment) */
    public $type;

    /** @var int|string|null - ONLY FOR META. post|term ID for getting value */
    public $id;
    public string $file;
    public string $url;
    public string $ver = '1.1.0';

    public function __construct($field, string $type = 'post', $id = null)
    {
        $this->type = $type;
        $this->id = $id;

        if (!isset($this->id) && $type === 'post') {
            $this->id = get_the_ID();
        }

        // alias
        $field['value'] = $field['value'] ?? $field['val'] ?? null;
        $field['label'] = $field['title'] ?? $field['label'] ?? null;
        $field['custom_attributes'] = $field['custom_attributes'] ?? $field['attributes'] ?? $field['attr'] ?? $field['atts'] ?? null;

        $this->field = $this->validate_field_data($field);

        $this->file = self::file;
        $this->url = plugin_dir_url($this->file);
        $this->ver = defined('WP_DEBUG') && WP_DEBUG ? time() : $this->ver;
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
     * Get html
     *
     * @param $output
     * @return false|string|void
     */
    public function render($output = true)
    {
        if (!$output) {
            ob_start();
        }

        switch ($this->field['type']) {
            case 'checkbox':
                $this->checkbox($this->field);
                break;
            case 'number':
                $this->input_minmax($this->field);
                break;
            case 'editor':
                $this->editor($this->field);
                break;
            case 'media':
                $this->media();
                break;
            case 'radio':
                $this->radio($this->field);
                break;
            case 'select':
                $this->select($this->field);
                break;
            case 'textarea':
                $this->textarea($this->field);
                break;
            case 'date_time':
            case 'datetime':
                $this->date_time();
                break;
            case 'image_picker':
            case 'imagepicker':
                $this->image_picker($this->field);
                break;
            default:
                $this->input($this->field);
                break;
        }

        if (!$output) {
            return ob_get_clean();
        }
    }

    private function checkbox($field)
    {
        $input = sprintf(
            '<input type="checkbox" %s id="%s" name="%s" class="%s" %s>',
            $this->checked($field),
            $field['id'],
            $field['id'],//name
            $field['class'] ?? '',
            $field['custom_attributes'] ?? '',
        );

        if (empty($field['unwrap'])) {
            $field['label'] = $input . $field['label'];
        } else {
            echo $input;
        }

        $this->label($field);
        $this->description($field);
    }

    /**
     * Is checked
     *
     * @param array $field
     * @return string
     */
    private function checked($field)
    {
        $checked = '';
        if (!empty($field['checked']) || (!empty($field['default']) && ('yes' === $field['default'] || 'on' === $field['default']))) { // true, 'checked'
            $checked = 'checked';
        }

        $value = $field['value'] ?? $this->get_value($field['id']);
        if ($value === 'on' || $value === 'yes') {
            $checked = 'checked';
        } elseif (!empty($value)) {
            $checked = '';
        }

        return $checked;
    }

    /**
     * Render field's label.
     * __("{$field['title']}", 'iiko') for correct localization
     */
    private function label(array $field)
    {
        if (!empty($field['label'])) {
            printf(
                '<label for="%s" class="%s">%s</label>',
                $field['id'],
                $field['label-class'] ?? 'input-label',
                $field['label']
            );
        }
    }

    /**
     * Render description
     */
    private function description(array $field)
    {
        if (isset($field['label']) && !empty($field['desc'])) {
            printf('<p class="description">%s</p>', $field['desc']);
        }
    }

    /**
     * @param array $field
     */
    private function input_minmax($field)
    {
        $this->label($field);
        printf(
            '<input class="%s" id="%s" %s %s name="%s" %s type="%s" value="%s" %s>',
            $field['class'] ?? 'regular-text',
            $field['id'],
            isset($field['max']) ? "max='{$field['max']}'" : '',
            isset($field['min']) ? "min='{$field['min']}'" : '',
            $field['name'] ?? $field['id'],
            isset($field['step']) ? "step='{$field['step']}'" : '',
            $field['type'],
            $this->value($field),
            $this->get_custom_attribute_html($field) ?? ''
        );
        $this->description($field);
    }

    /**
     * Render custom attributes for field
     *
     * @param array $field
     *
     * @return string|void
     */
    private function get_custom_attribute_html(array $field)
    {
        $custom_attributes = [];
        if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {
            foreach ($field['custom_attributes'] as $attribute => $attribute_value) {
                $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
            }
            return implode(' ', $custom_attributes);
        }

        if (is_string($field['custom_attributes'])) {
            return $field['custom_attributes'];
        }
    }

    /**
     * Wp Editor
     * @param $field
     * @return void
     */
    private function editor($field)
    {
        $this->label($field);
        if (function_exists('wp_editor')) {
            wp_editor($this->value($field), $field['id'], [
                'wpautop'       => isset($field['wpautop']),
                'media_buttons' => isset($field['media-buttons']),
                'textarea_name' => $field['id'],
                'textarea_rows' => $field['rows'] ?? 20,
                'teeny'         => isset($field['teeny']),
            ]);
        } else {
            trigger_error('wp_editor not found');
        }
        $this->description($field);
    }

    /**
     * Image
     */
    private function media()
    {
        wp_enqueue_media();

        static $once;
        if (!$once && $once = 1) {
            add_action('admin_print_footer_scripts', [$this, 'media_js'], 99);
        }

        $this->value = $this->value($this->field);

        if (!$src = is_numeric($this->value) ? wp_get_attachment_url($this->value) : $this->value) {
            $src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4Igp3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIKdmlld0JveD0iMCwwLDI1NiwyNTYiCnN0eWxlPSJmaWxsOiMwMDAwMDA7Ij4KPGcgZmlsbD0iIzJjMzMzOCIgZmlsbC1ydWxlPSJub256ZXJvIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgc3Ryb2tlLWRhc2hhcnJheT0iIiBzdHJva2UtZGFzaG9mZnNldD0iMCIgZm9udC1mYW1pbHk9Im5vbmUiIGZvbnQtd2VpZ2h0PSJub25lIiBmb250LXNpemU9Im5vbmUiIHRleHQtYW5jaG9yPSJub25lIiBzdHlsZT0ibWl4LWJsZW5kLW1vZGU6IG5vcm1hbCI+PGcgdHJhbnNmb3JtPSJzY2FsZSg1LjEyLDUuMTIpIj48cGF0aCBkPSJNMTAsMTFjLTEuNjU3LDAgLTMsMS4zNDMgLTMsM3YyMmMwLDEuNjU3IDEuMzQzLDMgMywzaDMwYzEuNjU3LDAgMywtMS4zNDMgMywtM3YtMjJjMCwtMS42NTcgLTEuMzQzLC0zIC0zLC0zek0xMCwxMmgzMGMxLjEwNSwwIDIsMC44OTUgMiwydjE5LjA5MThsLTguNTI5MywtNy42Njk5MmMtMS4zMzMsLTEuMTk4IC0zLjM1NzUsLTEuMTk2MTQgLTQuNjg3NSwwLjAwNTg2bC02LjkzMTY0LDYuMjYzNjdsLTMuMzE4MzYsLTIuODM3ODljLTEuMzE0LC0xLjEyMyAtMy4yNDk1OSwtMS4xMjAxOSAtNC41NTg1OSwwLjAwNzgxbC01Ljk3NDYxLDUuMTQ2NDh2LTIwLjAwNzgxYzAsLTEuMTA1IDAuODk1LC0yIDIsLTJ6TTE2LDE3Yy0xLjY1NywwIC0zLDEuMzQzIC0zLDNjMCwxLjY1NyAxLjM0MywzIDMsM2MxLjY1NywwIDMsLTEuMzQzIDMsLTNjMCwtMS42NTcgLTEuMzQzLC0zIC0zLC0zeiI+PC9wYXRoPjwvZz48L2c+Cjwvc3ZnPg==';
        }

        $post_id = 0;
        if ('post' === $this->type) {
            $post_id = $this->value;
        }

        ?>

        <?php
        $this->label($this->field); ?>
        <img data-src="<?= $src ?>" src="<?= $src ?>" width="150" alt="<?= esc_attr($this->field['label']) ?>"
             style="background: white; box-shadow: 0 0 1px grey;"/>
        <div>
            <input type="hidden" name="<?= $this->field['id'] ?>" id="<?= $this->field['id'] ?>"
                   value="<?= $post_id ?>"/>
            <button type="submit" class="upload_image_button button"><?php
                esc_html_e('Upload/Add image'); ?></button>
            <button type="submit" class="remove_image_button button">×</button>
        </div>

        <?php
        $this->description($this->field);
    }

    /**
     * Radio
     * @param $field
     */
    private function radio($field)
    {
        printf('<label class="label">%s</label> %s', $field['label'], $this->radio_options($field));
    }

    /**
     * Radio
     *
     * @param $field
     *
     * @return string
     */
    private function radio_options($field)
    {
        $output = [];

        $options = $field['options'] ?? [];
        if (is_string($options)) {
            $options = explode("\r\n", $field['options']);
        }

        $i = 0;
        foreach ($options as $opt_val => $option_name) {
            $output[] = sprintf(
                '<label class="label"><input %s id="%s-%d" name="%s" type="radio" value="%s"> %s</label>',
                $this->radio_checked($field, $opt_val),
                $field['id'],
                $i, // uniq id
                $field['id'], // name
                $opt_val, // value
                $option_name // name
            );
            $i++;
        }
        return implode('', $output); // br
    }

    /**
     * Checked?
     *
     * @param $field
     * @param $current
     *
     * @return string
     */
    private function radio_checked($field, $current)
    {
        $value = $this->value($field);
        if ($value === $current) {
            return 'checked';
        }
        return '';
    }

    /**
     * Get value
     *
     * @param array $field
     *
     * @return false|mixed|string|void
     */
    private function value(array $field)
    {
        $value = $field['value'] ?? $field['val'] ?? $field['default'] ?? '';

        //  'post', 'comment', 'term', 'user' || option
        if ('option' === $this->type || metadata_exists($this->type, $this->id, $field['id'])) {
            $db_value = $this->get_value($field['id'], $this->id);
            /*if( is_array($db_value)) {
                $db_value =  json_encode($db_value, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
            }*/
        }

        return $db_value ?? $value;
    }

    /**
     * Get field data by type
     *
     * @param string $key - option|meta name
     * @param int $id - object ID
     */
    public function get_value(string $key, $id = null)
    {
        $id = absint($id);
        switch ($this->type) {
            case 'option':
                $r = get_option($key, null);
                break;
            case 'term':
                $r = get_term_meta($id, $key, true);
                break;
            case 'user':
                $r = get_user_meta($id, $key, true);
                break;
            case 'comment':
                $r = get_comment_meta($id, $key, true);
                break;
            default:
                $r = get_post_meta(get_the_ID(), $key, true);
        }
        return $r;
    }

    /**
     * Select
     *
     * @param $field
     *
     * @return void
     */
    private function select($field)
    {
        $this->label($field);
        printf(
            '<select id="%s" name="%s" class="%s" %s %s >%s</select>',
            $field['id'],
            $field['id'],
            $field['class'] ?? '',
            isset($field['multiple']) ? 'multiple="multiple"' : '',
            $this->get_custom_attribute_html($field) ?? '',
            $this->select_options($field)
        );
        $this->description($field);
    }

    /**
     * Select choose options
     */
    private function select_options($field)
    {
        $output = [];

        $field['options'] = $field['options'] ?? [];

        $options = $field['options'];
        if (is_string($field['options'])) {
            $options = explode("\r\n", $field['options']);
        }

        $values = $this->value($field);// value or array of values

        $i = 0;
        foreach ($options as $opt_val => $option) {
            // options like string
            if (isset($field['parse_options']) && strpos($option, ':')) {
                $pair = explode(':', $option);
                $pair = array_map('trim', $pair);
                $output[] = sprintf(
                    '<option %s value="%s"> %s</option>',
                    $this->select_selected($field, $pair[0]),
                    $pair[0],
                    $pair[1]
                );
                continue;
            }

            $option_title = $option;
            if (isset($field['imagepicker'])) {
                $attr = 'data-img-src="' . esc_url($option) . '"';
            }

            // array
            $output[] = sprintf(
                '<option %s value="%s" %s > %s</option>',
                $this->select_selected($values, $opt_val),
                $opt_val,
                $attr ?? '',
                $option_title
            );

            $i++;
        }

        return implode('<br>', $output);
    }

    /**
     * @param string $current_option - value of current option
     * @param array|string $selected_values - array if multiply or string if single select
     */
    private function select_selected($selected_values, string $current_option)
    {
        if (is_array($selected_values) && in_array($current_option, $selected_values)) {
            return 'selected';
        }

        if (is_string($selected_values) && $selected_values === $current_option) {
            return 'selected';
        }

        return '';
    }

    /**
     * Text area generator
     * @param $field
     */
    private function textarea($field)
    {
        $this->label($field);
        printf(
            '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
            $field['id'],
            $field['id'],
            $field['rows'] ?? 5,
            $this->value($field)
        );
        $this->description($field);
    }

    /**
     * Select date and time field
     *
     * @return void
     */
    private function date_time()
    {
        add_action('admin_print_footer_scripts', [$this, 'date_time_js'], 99);

        $this->field['placeholder'] = $this->field['placeholder'] ?? 'Select date and time';
        $this->field['class'] = $this->field['class'] ?? 'regular-text datetime';

        $this->input($this->field);
    }

    /**
     * Simple text field. render
     * @param array $field
     */
    private function input($field)
    {
        if ($field['type'] === 'media') {
            $field['type'] = 'text';
        }

        if (isset($field['color-picker'])) {
            $field['class'] = 'wp-color-picker';
        }

        $this->label($field);
        printf(
            '<input class="%s" id="%s" name="%s" %s placeholder="%s" type="%s" value="%s">',
            $field['class'] ?? 'regular-text',
            $field['id'],
            $field['id'],
            $this->get_custom_attribute_html($field),
            $field['placeholder'] ?? '',
            $field['type'],
            $this->value($field)
        );
        $this->description($field);
    }

    /**
     * Image picker field
     *
     * Params:
     * data-limit:  int    Лимит выбора
     * show-labels: true  Показ описания под картинкой
     * masonry:     true  CSS Masonry
     *
     * @param array $field options as k => src
     */
    private function image_picker($field)
    {
        add_action('admin_print_footer_scripts', [$this, 'image_picker_js'], 99);

        $field['imagepicker'] = true;
        $field['class'] = !empty($field['class']) ? $field['class'] . ' image_picker' : 'image_picker';

        // validate
        foreach ($field['options'] as $option) {
            if (!is_array($option) || empty($option['src'])) {
                //trigger_error('Missing src for option image picker field');
            } else {
                // short to full data attr
                $option['data-img-src'] = $option['src'] ?? '';
                $option['data-img-label'] = $option['label'] ?? '';
                $option['data-img-class'] = $option['class'] ?? '';
                $option['data-img-alt'] = $option['alt'] ?? '';
                unset($option['src'], $option['alt'], $option['class'], $option['label']);
            }
        }

        $this->select($field);
    }

    /**
     * Output JS fo img upload
     *
     * @return void
     */
    public function media_js()
    {
        ?>
        <script>
            jQuery(function ($) {
                $('.upload_image_button').click(function (event) {
                    event.preventDefault();

                    const button = $(this);
                    const customUploader = wp.media({
                        title: '<?php esc_html_e('Select image')?>',
                        library: {
                            uploadedTo: button.prev().val(), // если для метобокса и хотим прилепить к текущему посту
                            type: 'image'
                        },
                        button: {
                            text: '<?php esc_html_e('Choose image')?>' // текст кнопки, по умолчанию "Вставить в запись"
                        },
                        multiple: false
                    });

                    // добавляем событие выбора изображения
                    customUploader.on('select', function () {
                        const image = customUploader.state().get('selection').first().toJSON();
                        button.parent().prev().attr('src', image.url);
                        button.prev().val(image.id);
                    });

                    // и открываем модальное окно с выбором изображения
                    customUploader.open();
                });

                $('.remove_image_button').click(function (event) {
                    event.preventDefault();

                    if (true === confirm('<?php esc_html_e('Sure?')?>')) {
                        const src = $(this).parent().prev().data('src');
                        $(this).parent().prev().attr('src', src);
                        $(this).prev().prev().val('');
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * Date time inline JS
     *
     * @return void
     */
    public function date_time_js()
    {
        $src[] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js';
        $src[] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css';
        ?>
        <link href="<?= $src[1] ?>" rel="stylesheet"/>
        <script src="<?= $src[0] ?>"></script>

        <script>
            jQuery(function ($) {
                var myControl = {
                    create: function (tp_inst, obj, unit, val, min, max, step) {
                        $('<input class="ui-timepicker-input" value="' + val + '" style="width:50%">')
                            .appendTo(obj)
                            .spinner({
                                min: min,
                                max: max,
                                step: step,
                                change: function (e, ui) {
                                    if (e.originalEvent !== undefined)
                                        tp_inst._onTimeChange();
                                    tp_inst._onSelectHandler();
                                },
                                spin: function (e, ui) {
                                    tp_inst.control.value(tp_inst, obj, unit, ui.value);
                                    tp_inst._onTimeChange();
                                    tp_inst._onSelectHandler();
                                }
                            });
                        return obj;
                    },
                    options: function (tp_inst, obj, unit, opts, val) {
                        if (typeof (opts) == 'string' && val !== undefined)
                            return obj.find('.ui-timepicker-input').spinner(opts, val);
                        return obj.find('.ui-timepicker-input').spinner(opts);
                    },
                    value: function (tp_inst, obj, unit, val) {
                        if (val !== undefined)
                            return obj.find('.ui-timepicker-input').spinner('value', val);
                        return obj.find('.ui-timepicker-input').spinner('value');
                    }
                };
                if ($.isFunction($.fn.datetimepicker)) {
                    $(".datetime").datetimepicker({controlType: myControl});
                } else {
                    console.error('You need enqueue script "jquery-ui" and "jquery-ui-timepicker-addon" for this page ');
                }
            });
        </script>
        <?php
    }

    /**
     * Image picker js
     *
     * @return void
     */
    public function image_picker_js()
    {
        $src[] = $this->url . 'assets/js/imagepicker.js';
        $src[] = $this->url . 'assets/css/imagepicker.css';
        ?>
        <script src="<?= $src[0] ?>"></script>
        <link href="<?= $src[1] ?>" rel="stylesheet"/>
        <script>
            jQuery(function ($) {
                $(".image_picker").imagepicker();
            });
        </script>
        <?php
    }
}
