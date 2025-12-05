<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit\Framework\TestCase class.
| By default, that will be PestPHP\Pest\TestCase. Of course, you may need to bind a different class for certain
| file or directories. You can do so here.
|
*/

use PHPUnit\Framework\TestCase;

uses(TestCase::class)->in('tests');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of expectations that you can apply to your values.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Feel free to use this space to define helper functions that you find yourself
| regularly needing within your tests. They're shared across every test file in your
| application, giving you a convenient place to reference them.
|
*/

function createField(array $config = []): array
{
    return array_merge([
        'id'    => 'test_field',
        'type'  => 'text',
        'label' => 'Test Field',
    ], $config);
}

function createWPField(array $config = [], string $storage_type = 'options'): \WP_Field
{
    return new \WP_Field(createField($config), $storage_type);
}

/*
|--------------------------------------------------------------------------
| WordPress Mock Functions for Testing
|--------------------------------------------------------------------------
*/

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://example.com/wp-content/plugins/woo2iiko/lib/wp-field/';
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_textarea')) {
    function esc_textarea($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('tag_escape')) {
    function tag_escape($tag) {
        return htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('absint')) {
    function absint($maybeint) {
        return abs((int) $maybeint);
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

if (!function_exists('get_post_meta')) {
    function get_post_meta($post_id, $key, $single = false) {
        return null;
    }
}

if (!function_exists('get_term_meta')) {
    function get_term_meta($term_id, $key, $single = false) {
        return null;
    }
}

if (!function_exists('get_user_meta')) {
    function get_user_meta($user_id, $key, $single = false) {
        return null;
    }
}

if (!function_exists('get_comment_meta')) {
    function get_comment_meta($comment_id, $key, $single = false) {
        return null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return false;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
        return true;
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
        return true;
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($tag, $value, ...$args) {
        return $value;
    }
}

if (!function_exists('get_the_ID')) {
    function get_the_ID() {
        return 1;
    }
}
