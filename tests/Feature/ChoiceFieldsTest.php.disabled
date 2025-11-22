<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ChoiceFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_renders_select_with_options(): void
    {
        $html = \WP_Field::make([
            'id'      => 'country',
            'type'    => 'select',
            'label'   => 'Country',
            'options' => [
                'ru' => 'Russia',
                'us' => 'USA',
                'uk' => 'UK',
            ],
        ], false);

        expect($html)->toContain('Russia');
        expect($html)->toContain('USA');
        expect($html)->toContain('UK');
    }

    /** @test */
    public function it_renders_multiselect(): void
    {
        $html = \WP_Field::make([
            'id'      => 'features',
            'type'    => 'multiselect',
            'label'   => 'Features',
            'options' => ['a' => 'Feature A', 'b' => 'Feature B'],
        ], false);

        expect($html)->toContain('multiple="multiple"');
        expect($html)->toContain('Feature A');
        expect($html)->toContain('Feature B');
    }

    /** @test */
    public function it_renders_radio_with_options(): void
    {
        $html = \WP_Field::make([
            'id'      => 'delivery',
            'type'    => 'radio',
            'label'   => 'Delivery Type',
            'options' => [
                'courier' => 'Courier',
                'pickup'  => 'Pickup',
            ],
        ], false);

        expect($html)->toContain('type="radio"');
        expect($html)->toContain('Courier');
        expect($html)->toContain('Pickup');
    }

    /** @test */
    public function it_renders_checkbox_group(): void
    {
        $html = \WP_Field::make([
            'id'      => 'notifications',
            'type'    => 'checkbox_group',
            'label'   => 'Notifications',
            'options' => [
                'sms'   => 'SMS',
                'email' => 'Email',
                'push'  => 'Push',
            ],
        ], false);

        expect($html)->toContain('wp-field-checkbox-group');
        expect($html)->toContain('SMS');
        expect($html)->toContain('Email');
        expect($html)->toContain('Push');
    }

    /** @test */
    public function it_renders_select_with_selected_value(): void
    {
        $html = \WP_Field::make([
            'id'      => 'status',
            'type'    => 'select',
            'label'   => 'Status',
            'value'   => 'active',
            'options' => [
                'active'   => 'Active',
                'inactive' => 'Inactive',
            ],
        ], false);

        expect($html)->toContain('selected');
    }

    /** @test */
    public function it_renders_radio_with_checked_value(): void
    {
        $html = \WP_Field::make([
            'id'      => 'choice',
            'type'    => 'radio',
            'label'   => 'Choice',
            'value'   => 'yes',
            'options' => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
        ], false);

        expect($html)->toContain('checked');
    }

    /** @test */
    public function it_renders_checkbox_group_with_multiple_values(): void
    {
        $html = \WP_Field::make([
            'id'      => 'tags',
            'type'    => 'checkbox_group',
            'label'   => 'Tags',
            'value'   => ['tag1', 'tag2'],
            'options' => [
                'tag1' => 'Tag 1',
                'tag2' => 'Tag 2',
                'tag3' => 'Tag 3',
            ],
        ], false);

        expect($html)->toContain('checkbox_group');
        expect($html)->toContain('Tag 1');
        expect($html)->toContain('Tag 2');
        expect($html)->toContain('Tag 3');
    }

    /** @test */
    public function it_supports_parse_options(): void
    {
        $html = \WP_Field::make([
            'id'            => 'parsed',
            'type'          => 'select',
            'label'         => 'Parsed Options',
            'parse_options' => true,
            'options'       => [
                'Option 1:opt1',
                'Option 2:opt2',
            ],
        ], false);

        expect($html)->toContain('Option 1');
        expect($html)->toContain('Option 2');
    }

    /** @test */
    public function it_renders_radio_group_with_labels(): void
    {
        $html = \WP_Field::make([
            'id'      => 'type',
            'type'    => 'radio',
            'label'   => 'Type',
            'options' => [
                'type_a' => 'Type A',
                'type_b' => 'Type B',
                'type_c' => 'Type C',
            ],
        ], false);

        expect($html)->toContain('wp-field-radio-group');
        expect($html)->toContain('Type A');
        expect($html)->toContain('Type B');
        expect($html)->toContain('Type C');
    }
}
