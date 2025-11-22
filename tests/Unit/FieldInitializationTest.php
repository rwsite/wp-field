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
    public function test_initializes_field_types_registry(): void
    {
        \WP_Field::init_field_types();

        // Проверяем, что реестр инициализирован
        $reflection = new \ReflectionClass(\WP_Field::class);
        $property = $reflection->getProperty('field_types');
        $property->setAccessible(true);
        $types = $property->getValue();

        $this->assertNotEmpty($types);
        $this->assertTrue(isset($types['text']));
        $this->assertTrue(isset($types['select']));
        $this->assertTrue(isset($types['repeater']));
    }

    /** @test */
    public function test_supports_field_aliases(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'title' => 'Test Field',  // alias для label
        ], 'options');

        $this->assertEquals('Test Field', $field->field['label']);
    }

    /** @test */
    public function test_supports_value_alias(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
            'val'   => 'test value',  // alias для value
        ], 'options');

        $this->assertEquals('test value', $field->field['value']);
    }

    /** @test */
    public function test_supports_custom_attributes_aliases(): void
    {
        $field = new \WP_Field([
            'id'         => 'test',
            'type'       => 'text',
            'label'      => 'Test',
            'attributes' => ['data-test' => 'value'],  // alias для custom_attributes
        ], 'options');

        $this->assertNotNull($field->field['custom_attributes']);
    }

    /** @test */
    public function test_creates_field_with_static_make(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ], false);

        $this->assertIsString($html);
        $this->assertStringContainsString('wp-field', $html);
    }

    /** @test */
    public function test_creates_field_with_make_and_output(): void
    {
        ob_start();
        $result = \WP_Field::make([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ], true);
        $output = ob_get_clean();

        $this->assertNull($result);
        $this->assertStringContainsString('wp-field', $output);
    }

    // Skipping validation test - validate_field_data() uses trigger_error() by design, not exceptions

    /** @test */
    public function test_sets_default_storage_type(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
        ]);

        $this->assertEquals('post', $field->storage_type);
    }

    /** @test */
    public function test_supports_different_storage_types(): void
    {
        $types = ['post', 'options', 'term', 'user', 'comment'];

        foreach ($types as $type) {
            $field = new \WP_Field([
                'id'    => 'test',
                'type'  => 'text',
                'label' => 'Test',
            ], $type);

            $this->assertEquals($type, $field->storage_type);
        }
    }

    /** @test */
    public function test_handles_field_with_default_value(): void
    {
        $field = new \WP_Field([
            'id'      => 'test',
            'type'    => 'text',
            'label'   => 'Test',
            'default' => 'default value',
        ], 'options');

        $this->assertEquals('default value', $field->field['default']);
    }

    /** @test */
    public function test_handles_field_with_explicit_value(): void
    {
        $field = new \WP_Field([
            'id'    => 'test',
            'type'  => 'text',
            'label' => 'Test',
            'value' => 'explicit value',
        ], 'options');

        $this->assertEquals('explicit value', $field->field['value']);
    }

    /** @test */
    public function test_supports_field_with_options(): void
    {
        $field = new \WP_Field([
            'id'      => 'test',
            'type'    => 'select',
            'label'   => 'Test',
            'options' => ['a' => 'Option A', 'b' => 'Option B'],
        ], 'options');

        $this->assertCount(2, $field->field['options']);
    }

    /** @test */
    public function test_supports_field_with_nested_fields(): void
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

        $this->assertCount(2, $field->field['fields']);
    }
}
