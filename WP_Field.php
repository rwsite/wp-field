<?php
/**
 * Plugin Name: Universal html field generator for WP
 * Plugin URI: https://rwsite.ru
 * Description: Library. Universal HTML field generator for WordPress. See usage examples in radme.md
 * Version: 1.0.0
 * Author: Aleksey Tikhomirov
 * Author URI: https://rwsite.ru
 * Text Domain: wp-field
 * Domain Path: /lang
 *
 * @package wp-field
 */

if(!defined('ABSPATH') || class_exists('WP_Field')) {
    return;
}

class WP_Field
{
    /**
     * @var $field array{ id: string, type: string, label: string, class: string, custom_attributes: array }
     *     'id': meta-key|option name
     *     'options': array - for select
     *     'parse_options' - parse text options as: option:value
     */
    public $field;

    /** @var string|null - placement type (post meta|options|term meta|user|comment) */
    public $type;

    /** @var int|null - ONLY FOR META. post|term ID for getting value */
    public $id;

    public function __construct( $field, string $type = 'post', $id = null)
    {
        $this->type = $type;
        $this->id = $id;

        if(!isset($this->id) && $type === 'post') {
            $this->id = get_the_ID();
        }

        $this->validate_field_data($field);

        // alias
        $field['title'] = $field['title'] ?? $field['label'] ?? null;
        $field['custom_attributes'] = $field['custom_attributes'] ?? $field['attributes'] ?? $field['attr'] ?? null;

        switch ( $field['type'] ) {
            case 'checkbox':
                $this->checkbox( $field );
                break;
            case 'number':
                $this->input_minmax( $field );
                break;
            case 'editor':
                $this->editor( $field );
                break;
            case 'media':
                $this->input( $field );
                $this->media_button( $field );
                break;
            case 'radio':
                $this->radio( $field );
                break;
            case 'select':
                $this->select( $field );
                break;
            case 'textarea':
                $this->textarea( $field );
                break;
            default:
                $this->input( $field );
        }
    }


    private function checkbox( $field ) {
        printf(
            '<label class="checkbox-label"><input %s id="%s" name="%s" type="checkbox"> %s</label>',
            $this->checked( $field ),
            $field['id'], $field['id'],
            isset( $field['title'] ) ? $field['title'] : ''
        );
        $this->description($field);
    }

    private function editor( $field ) {
        $this->label($field);
        if(function_exists('wp_editor')) {
            wp_editor($this->value($field), $field['id'], [
                'wpautop'       => isset($field['wpautop']) ? true : false,
                'media_buttons' => isset($field['media-buttons']) ? true : false,
                'textarea_name' => $field['id'],
                'textarea_rows' => isset($field['rows']) ? isset($field['rows']) : 20,
                'teeny'         => isset($field['teeny']) ? true : false
            ]);
        } else {
            trigger_error('wp_editor not found');
        }
        $this->description($field);
    }

    /** render simple text field */
    private function input( $field ) {
        if ( $field['type'] === 'media' ) {
            $field['type'] = 'text';
        }
        if ( isset( $field['color-picker'] ) ) {
            $field['class'] = 'rwp-color-picker';
        }
        $this->label($field);
        printf(
            '<input class="%s" id="%s" name="%s" %s placeholder="%s" type="%s" value="%s">',
            isset( $field['class'] ) ? $field['class'] : 'regular-text',
            $field['id'], $field['id'],
            isset( $field['custom_attributes'] ) || isset($field['attr']) ? $this->get_custom_attribute_html($field) : '',
            $field['placeholder'] ?? '',
            $field['type'],
            $this->value( $field )
        );
        $this->description($field);
    }

    private function input_minmax( $field ) {
        $this->label($field);
        printf(
            '<input class="%s" id="%s" %s %s name="%s" %s type="%s" value="%s" %s>',
            isset($field['class']) ? $field['class'] : 'regular-text',
            $field['id'],
            isset( $field['max'] ) ? "max='{$field['max']}'" : '',
            isset( $field['min'] ) ? "min='{$field['min']}'" : '',
            $field['name'] ?? $field['id'],
            isset( $field['step'] ) ? "step='{$field['step']}'" : '',
            $field['type'],
            $this->value( $field ),
            isset( $field['custom_attributes'] ) ? $this->get_custom_attribute_html($field) : ''
        );
        $this->description($field);
    }

    private function media_button( $field ) {
        printf(
            ' <button class="button rwp-media-toggle" data-modal-button="%s" data-modal-title="%s" data-return="%s" id="%s_button" name="%s_button" type="button">%s</button>',
            isset( $field['modal-button'] ) ? $field['modal-button'] : __( 'Select this file', 'advanced-options' ),
            isset( $field['modal-title'] ) ? $field['modal-title'] : __( 'Choose a file', 'advanced-options' ),
            $field['return'],
            $field['id'], $field['id'],
            isset( $field['button-text'] ) ? $field['button-text'] : __( 'Upload', 'advanced-options' )
        );
    }

    private function radio( $field ) {
        printf(
            '<fieldset><legend class="screen-reader-text">%s</legend>%s</fieldset>',
            $field['label'],
            $this->radio_options( $field )
        );
    }

    private function radio_checked( $field, $current ) {
        $value = $this->value( $field );
        if ( $value === $current ) {
            return 'checked';
        }
        return '';
    }

    private function radio_options( $field ) {
        $output = [];
        $options = explode( "\r\n", $field['options'] );
        $i = 0;
        foreach ( $options as $option ) {
            $pair = explode( ':', $option );
            $pair = array_map( 'trim', $pair );
            $output[] = sprintf(
                '<label class="label"><input %s id="%s-%d" name="%s" type="radio" value="%s"> %s</label>',
                $this->radio_checked( $field, $pair[0] ),
                $field['id'], $i, $field['id'],
                $pair[0], $pair[1]
            );
            $i++;
        }
        return implode( '<br>', $output );
    }

    /** Select */
    private function select( $field ) {
        $this->label($field);
        printf(
            '<select id="%s" name="%s" %s %s>%s</select>',
            $field['id'], $field['id'],
            isset($field['multiple']) ? 'multiple="multiple"' : '',
            isset($field['custom_attributes']) ? $this->get_custom_attribute_html($field) : '',
            $this->select_options( $field )
        );
        $this->description($field);
    }

    /**
     * @param string $current_option - value of current option
     * @param array|string $selected_values - array if multiply or string if single select
     */
    private function select_selected( $selected_values, string $current_option ) {

        if(is_array($selected_values) && in_array($current_option, $selected_values)){
            return 'selected';
        }

        if (is_string($selected_values) && $selected_values === $current_option ) {
            return 'selected';
        }

        return '';
    }

    /* Select choose options */
    private function select_options( $field ) {

        $output = [];

        $field['options'] = $field['options'] ?? [];

        $options = $field['options'];
        if(is_string($field['options'])) {
            $options = explode("\r\n", $field['options']);
        }

        $values = $this->value( $field );// value or array of values

        $i = 0;
        foreach ( $options as $opt_val => $option_name ) {
            // options like string
            if( isset($field['parse_options']) && strpos($option_name,':')) {
                $pair = explode( ':', $option_name );
                $pair = array_map('trim', $pair);
                $output[] = sprintf(
                    '<option %s value="%s"> %s</option>',
                    $this->select_selected($field, $pair[0]),
                    $pair[0], $pair[1]
                );
                continue;
            }

            // array
            $output[] = sprintf(
                '<option %s value="%s"> %s</option>',
                $this->select_selected($values, $opt_val),
                $opt_val, $option_name
            );

            $i++;
        }

        return implode( '<br>', $output );
    }

    private function textarea( $field ) {

        $this->label($field);
        printf(
            '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
            $field['id'], $field['id'],
            $field['rows'] ?? 5,
            $this->value( $field )
        );
        $this->description($field);
    }

    private function checked( $field ) {
        if ( 'option' === $this->type || metadata_exists( 'post', $this->id, $field['id'] ) ) {
            $value = $this->get_value($field['id']);
            if ( $value === 'on' || $value === 'yes' ) {
                return 'checked';
            }
            return '';
        } else if ( !empty($field['checked']) ) { // true, 'checked'
            return 'checked';
        }
        return '';
    }

    /**
     * Render custom attributes for field
     *
     * @param array $field
     * @return string|void
     */
    private function get_custom_attribute_html( array $field ) {
        $custom_attributes = [];
        $field['custom_attributes'] = $field['custom_attributes'] ?? $field['attr'];// slug
        if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
            foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
            return implode( ' ', $custom_attributes );
        }
    }

    /**
     * Get value
     *
     * @param array $field
     * @return false|mixed|string|void
     */
    private function value( array $field ) {

        $value = $field['value'] ?? $field['val'] ?? $field['default'] ?? '';

        //  'post', 'comment', 'term', 'user' || option
        if ( 'option' === $this->type || metadata_exists( $this->type, $this->id, $field['id']) ){
            $db_value = $this->get_value($field['id'], $this->id);
            /*if( is_array($db_value)) {
                $db_value =  json_encode($db_value, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
            }*/
        }

        return $db_value ?? $value;
    }

    /**
     * Render field's label.
     * __("{$field['title']}", 'iiko') for correct localization
     */
    private function label( array $field ){
        if(isset($field['title']) && !empty($field['title'])) {
           echo '<label for="'.$field['id'].'" class="label">' . __("{$field['title']}", 'iiko') .'</label>';
        }
    }

    /** render description */
    private function description( array $field ){
        if(isset($field['title']) && !empty($field['desc']))
        printf('<span class="description">%s</span>', $field['desc'] );
    }

    /**
     * Get field data by type
     *
     * @param string $key - option|meta name
     * @param int $id - object ID
     */
    public function get_value( string $key, $id = null){
        $id = absint($id);
        switch ( $this->type ) {
            case 'option':
                $r = get_option( $key, null);
                break;
            case 'term':
                $r = get_term_meta($id, $key, true );
                break;
            case 'user':
                $r = get_user_meta($id, $key, true );
                break;
            case 'comment':
                $r = get_comment_meta($id, $key, true );
                break;
            default:
                $r = get_post_meta( get_the_ID(), $key, true);
        }
        return $r;
    }

    /**
     * Validate field data before rendering
     */
    private function validate_field_data($field){
        if(is_object($field)){
            $field = get_object_vars($field);
        }
        if(is_string($field)){
            $str = $field; $field = [];
            parse_str($str, $field);
        }
        if(!is_array($field)){
            return trigger_error('Wrong field data!');
        }
        if(empty($field['id']) || empty($field['type']) || empty($field)){
            return trigger_error('Incorrect field data');
        }
        return $field;
    }
}