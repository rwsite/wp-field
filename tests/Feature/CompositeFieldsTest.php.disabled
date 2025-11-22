<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class CompositeFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_renders_group_field(): void
    {
        $html = \WP_Field::make([
            'id'    => 'address',
            'type'  => 'group',
            'label' => 'Address',
            'fields' => [
                ['id' => 'city', 'type' => 'text', 'label' => 'City'],
                ['id' => 'street', 'type' => 'text', 'label' => 'Street'],
            ],
        ], false);

        expect($html)->toContain('wp-field-group');
        expect($html)->toContain('City');
        expect($html)->toContain('Street');
    }

    /** @test */
    public function it_renders_repeater_field(): void
    {
        $html = \WP_Field::make([
            'id'       => 'work_times',
            'type'     => 'repeater',
            'label'    => 'Work Times',
            'min'      => 1,
            'max'      => 7,
            'fields'   => [
                ['id' => 'day', 'type' => 'select', 'label' => 'Day', 'options' => ['mon' => 'Monday']],
                ['id' => 'from', 'type' => 'time', 'label' => 'From'],
            ],
        ], false);

        expect($html)->toContain('wp-field-repeater');
        expect($html)->toContain('wp-field-repeater-add');
        expect($html)->toContain('Monday');
    }

    /** @test */
    public function it_renders_repeater_with_min_max(): void
    {
        $html = \WP_Field::make([
            'id'       => 'items',
            'type'     => 'repeater',
            'label'    => 'Items',
            'min'      => 2,
            'max'      => 5,
            'fields'   => [
                ['id' => 'name', 'type' => 'text', 'label' => 'Name'],
            ],
        ], false);

        expect($html)->toContain('data-min="2"');
        expect($html)->toContain('data-max="5"');
    }

    /** @test */
    public function it_renders_repeater_add_button(): void
    {
        $html = \WP_Field::make([
            'id'       => 'items',
            'type'     => 'repeater',
            'label'    => 'Items',
            'add_text' => 'Add Item',
            'fields'   => [
                ['id' => 'name', 'type' => 'text', 'label' => 'Name'],
            ],
        ], false);

        expect($html)->toContain('Add Item');
        expect($html)->toContain('wp-field-repeater-add');
    }

    /** @test */
    public function it_renders_group_with_nested_fields(): void
    {
        $html = \WP_Field::make([
            'id'    => 'contact',
            'type'  => 'group',
            'label' => 'Contact',
            'fields' => [
                ['id' => 'name', 'type' => 'text', 'label' => 'Name'],
                ['id' => 'email', 'type' => 'email', 'label' => 'Email'],
                ['id' => 'phone', 'type' => 'tel', 'label' => 'Phone'],
            ],
        ], false);

        expect($html)->toContain('wp-field-group');
        expect($html)->toContain('Name');
        expect($html)->toContain('Email');
        expect($html)->toContain('Phone');
    }

    /** @test */
    public function it_renders_repeater_with_select_options(): void
    {
        $html = \WP_Field::make([
            'id'       => 'schedule',
            'type'     => 'repeater',
            'label'    => 'Schedule',
            'fields'   => [
                [
                    'id'      => 'day',
                    'type'    => 'select',
                    'label'   => 'Day',
                    'options' => ['mon' => 'Monday', 'tue' => 'Tuesday'],
                ],
            ],
        ], false);

        expect($html)->toContain('Monday');
        expect($html)->toContain('Tuesday');
    }
}
