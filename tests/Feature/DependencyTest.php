<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class DependencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once dirname(__DIR__, 2) . '/WP_Field.php';
    }

    /** @test */
    public function it_hides_field_when_dependency_not_met(): void
    {
        $html = \WP_Field::make([
            'id'    => 'dependent_field',
            'type'  => 'text',
            'label' => 'Dependent Field',
            'dependency' => [
                ['other_field', '==', 'value'],
            ],
        ], false);

        expect($html)->toContain('is-hidden');
    }

    /** @test */
    public function it_shows_field_when_dependency_met(): void
    {
        // Создаём поле с явным значением, которое удовлетворяет зависимость
        $html = \WP_Field::make([
            'id'    => 'dependent_field',
            'type'  => 'text',
            'label' => 'Dependent Field',
            'value' => 'value',
            'dependency' => [
                ['dependent_field', '==', 'value'],
            ],
        ], false);

        // Поле должно быть видимо, т.к. зависимость удовлетворена
        expect($html)->not->toContain('is-hidden');
    }

    /** @test */
    public function it_renders_dependency_data_attribute(): void
    {
        $html = \WP_Field::make([
            'id'    => 'field_with_dep',
            'type'  => 'text',
            'label' => 'Field',
            'dependency' => [
                ['other_field', '==', 'value'],
            ],
        ], false);

        expect($html)->toContain('data-dependency');
    }

    /** @test */
    public function it_handles_multiple_dependencies_with_and(): void
    {
        $html = \WP_Field::make([
            'id'    => 'field_with_deps',
            'type'  => 'text',
            'label' => 'Field',
            'dependency' => [
                ['field1', '==', 'value1'],
                ['field2', '!=', 'value2'],
                'relation' => 'AND',
            ],
        ], false);

        expect($html)->toContain('data-dependency');
        expect($html)->toContain('AND');
    }

    /** @test */
    public function it_handles_multiple_dependencies_with_or(): void
    {
        $html = \WP_Field::make([
            'id'    => 'field_with_deps',
            'type'  => 'text',
            'label' => 'Field',
            'dependency' => [
                ['field1', '==', 'value1'],
                ['field2', '==', 'value2'],
                'relation' => 'OR',
            ],
        ], false);

        expect($html)->toContain('data-dependency');
        expect($html)->toContain('OR');
    }

    /** @test */
    public function it_supports_in_operator(): void
    {
        $html = \WP_Field::make([
            'id'    => 'field_with_in',
            'type'  => 'text',
            'label' => 'Field',
            'dependency' => [
                ['field', 'in', ['a', 'b', 'c']],
            ],
        ], false);

        expect($html)->toContain('data-dependency');
    }

    /** @test */
    public function it_supports_contains_operator(): void
    {
        $html = \WP_Field::make([
            'id'    => 'field_with_contains',
            'type'  => 'text',
            'label' => 'Field',
            'dependency' => [
                ['field', 'contains', 'text'],
            ],
        ], false);

        expect($html)->toContain('data-dependency');
    }

    /** @test */
    public function it_supports_empty_operator(): void
    {
        $html = \WP_Field::make([
            'id'    => 'field_with_empty',
            'type'  => 'text',
            'label' => 'Field',
            'dependency' => [
                ['field', 'empty', null],
            ],
        ], false);

        expect($html)->toContain('data-dependency');
    }

    /** @test */
    public function it_supports_comparison_operators(): void
    {
        $operators = ['==', '!=', '>', '>=', '<', '<='];

        foreach ($operators as $op) {
            $html = \WP_Field::make([
                'id'    => 'field_with_op',
                'type'  => 'text',
                'label' => 'Field',
                'dependency' => [
                    ['field', $op, 'value'],
                ],
            ], false);

            expect($html)->toContain('data-dependency');
        }
    }
}
