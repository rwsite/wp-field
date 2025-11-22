<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FieldInitializationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_initializes_field_types_registry(): void
    {
        \WP_Field::init_field_types();

        // Проверяем, что реестр инициализирован
        $reflection = new \ReflectionClass(\WP_Field::class);
        $property = $reflection->getProperty('field_types');
        $property->setAccessible(true);
        $types = $property->getValue();

        expect($types)->not->toBeEmpty();
        expect(isset($types['text']))->toBeTrue();
        expect(isset($types['select']))->toBeTrue();
        expect(isset($types['repeater']))->toBeTrue();
    }

    /** @test */
    public function it_supports_field_aliases(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'title' => 'Test Field',  // alias для label
        ], 'options');

        expect($field->field['label'])->toBe('Test Field');
    }

    /** @test */
    public function it_supports_value_alias(): void
    {
        $field = new \WP_Field([
            'id'   => 'test',
            'type' => 'text',
            'val'  => 'test value',  // alias для value
        ], 'options');

        expect($field->field['value'])->toBe('test value');
    }

    /** @test */
    public function it_supports_custom_attributes_aliases(): void
    {
        $field = new \WP_Field([
            'id'         => 'test',
            'type'       => 'text',
            'attributes' => ['data-test' => 'value'],  // alias для custom_attributes
        ], 'options');

        expect($field->field['custom_attributes'])->not->toBeNull();
    }

    /** @test */
    public function it_creates_field_with_static_make(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ], false);

        expect($html)->toBeString();
        expect($html)->toContain('wp-field');
    }

    /** @test */
    public function it_creates_field_with_make_and_output(): void
    {
        ob_start();
        $result = \WP_Field::make([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ], true);
        $output = ob_get_clean();

        expect($result)->toBeNull();
        expect($output)->toContain('wp-field');
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        $this->expectException(\ErrorException::class);

        new \WP_Field([
            'type'  => 'text',
            'label' => 'Test',
            // Missing 'id'
        ], 'options');
    }

    /** @test */
    public function it_sets_default_storage_type(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ]);

        expect($field->storage_type)->toBe('post');
    }

    /** @test */
    public function it_supports_different_storage_types(): void
    {
        $types = ['post', 'options', 'term', 'user', 'comment'];

        foreach ($types as $type) {
            $field = new \WP_Field([
                'id'    => 'test',
                'type'  => 'text',
                'label' => 'Test',
            ], $type);

            expect($field->storage_type)->toBe($type);
        }
    }

    /** @test */
    public function it_handles_field_with_default_value(): void
    {
        $field = new \WP_Field([
            'id'      => 'test',
            'type'    => 'text',
            'label'   => 'Test',
            'default' => 'default value',
        ], 'options');

        expect($field->field['default'])->toBe('default value');
    }

    /** @test */
    public function it_handles_field_with_explicit_value(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
            'value' => 'explicit value',
        ], 'options');

        expect($field->field['value'])->toBe('explicit value');
    }

    /** @test */
    public function it_supports_field_with_options(): void
    {
        $field = new \WP_Field([
            'id'      => 'test',
            'type'    => 'select',
            'label'   => 'Test',
            'options' => ['a' => 'Option A', 'b' => 'Option B'],
        ], 'options');

        expect($field->field['options'])->toHaveCount(2);
    }

    /** @test */
    public function it_supports_field_with_nested_fields(): void
    {
        $field = new \WP_Field([
            'id'     => 'test',
            'type'   => 'group',
            'label'  => 'Test',
            'fields' => [
                ['id' => 'sub1', 'type' => 'text', 'label' => 'Sub 1'],
                ['id' => 'sub2', 'type' => 'text', 'label' => 'Sub 2'],
            ],
        ], 'options');

        expect($field->field['fields'])->toHaveCount(2);
    }
}
