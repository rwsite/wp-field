<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class FieldRenderingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_renders_text_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_text',
            'type'  => 'text',
            'label' => 'Test Text',
        ], false);

        expect($html)->toContain('wp-field');
        expect($html)->toContain('test_text');
        expect($html)->toContain('Test Text');
        expect($html)->toContain('type="text"');
    }

    /** @test */
    public function it_renders_select_field(): void
    {
        $html = \WP_Field::make([
            'id'      => 'test_select',
            'type'    => 'select',
            'label'   => 'Test Select',
            'options' => ['a' => 'Option A', 'b' => 'Option B'],
        ], false);

        expect($html)->toContain('<select');
        expect($html)->toContain('Option A');
        expect($html)->toContain('Option B');
        expect($html)->toContain('test_select');
    }

    /** @test */
    public function it_renders_radio_field(): void
    {
        $html = \WP_Field::make([
            'id'      => 'test_radio',
            'type'    => 'radio',
            'label'   => 'Test Radio',
            'options' => ['yes' => 'Yes', 'no' => 'No'],
        ], false);

        expect($html)->toContain('type="radio"');
        expect($html)->toContain('Yes');
        expect($html)->toContain('No');
    }

    /** @test */
    public function it_renders_checkbox_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_checkbox',
            'type'  => 'checkbox',
            'label' => 'Test Checkbox',
        ], false);

        expect($html)->toContain('type="checkbox"');
        expect($html)->toContain('test_checkbox');
    }

    /** @test */
    public function it_renders_textarea_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_textarea',
            'type'  => 'textarea',
            'label' => 'Test Textarea',
        ], false);

        expect($html)->toContain('<textarea');
        expect($html)->toContain('test_textarea');
    }

    /** @test */
    public function it_renders_number_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_number',
            'type'  => 'number',
            'label' => 'Test Number',
            'min'   => 0,
            'max'   => 100,
        ], false);

        expect($html)->toContain('type="number"');
        expect($html)->toContain('min="0"');
        expect($html)->toContain('max="100"');
    }

    /** @test */
    public function it_renders_email_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_email',
            'type'  => 'email',
            'label' => 'Test Email',
        ], false);

        expect($html)->toContain('type="email"');
        expect($html)->toContain('test_email');
    }

    /** @test */
    public function it_renders_color_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_color',
            'type'  => 'color',
            'label' => 'Test Color',
        ], false);

        expect($html)->toContain('wp-color-picker-field');
        expect($html)->toContain('test_color');
    }

    /** @test */
    public function it_renders_date_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_date',
            'type'  => 'date',
            'label' => 'Test Date',
        ], false);

        expect($html)->toContain('type="date"');
        expect($html)->toContain('test_date');
    }

    /** @test */
    public function it_renders_time_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_time',
            'type'  => 'time',
            'label' => 'Test Time',
        ], false);

        expect($html)->toContain('type="time"');
        expect($html)->toContain('test_time');
    }

    /** @test */
    public function it_renders_field_with_placeholder(): void
    {
        $html = \WP_Field::make([
            'id'          => 'test_field',
            'type'        => 'text',
            'label'       => 'Test',
            'placeholder' => 'Enter value',
        ], false);

        expect($html)->toContain('placeholder="Enter value"');
    }

    /** @test */
    public function it_renders_field_with_description(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_field',
            'type'  => 'text',
            'label' => 'Test',
            'desc'  => 'This is a description',
        ], false);

        expect($html)->toContain('This is a description');
        expect($html)->toContain('description');
    }

    /** @test */
    public function it_renders_field_with_custom_class(): void
    {
        $html = \WP_Field::make([
            'id'    => 'test_field',
            'type'  => 'text',
            'label' => 'Test',
            'class' => 'my-custom-class',
        ], false);

        expect($html)->toContain('my-custom-class');
    }

    /** @test */
    public function it_renders_field_with_custom_attributes(): void
    {
        $html = \WP_Field::make([
            'id'                 => 'test_field',
            'type'               => 'text',
            'label'              => 'Test',
            'custom_attributes'  => ['data-test' => 'value', 'aria-label' => 'Test Label'],
        ], false);

        expect($html)->toContain('data-test="value"');
        expect($html)->toContain('aria-label="Test Label"');
    }

    /** @test */
    public function it_renders_readonly_field(): void
    {
        $html = \WP_Field::make([
            'id'       => 'test_field',
            'type'     => 'text',
            'label'    => 'Test',
            'readonly' => true,
        ], false);

        expect($html)->toContain('readonly');
    }

    /** @test */
    public function it_renders_disabled_field(): void
    {
        $html = \WP_Field::make([
            'id'       => 'test_field',
            'type'     => 'text',
            'label'    => 'Test',
            'disabled' => true,
        ], false);

        expect($html)->toContain('disabled');
    }
}
