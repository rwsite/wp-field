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
