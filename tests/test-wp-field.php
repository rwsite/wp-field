<?php
/**
 * Простые тесты для WP_Field
 */

// Загружаем WordPress
require_once dirname(__DIR__, 6) . '/wp-load.php';

// Загружаем класс
require_once __DIR__ . '/../WP_Field.php';

class WP_Field_Tests {
    private $passed = 0;
    private $failed = 0;

    public function run() {
        echo "=== WP_Field Tests ===\n\n";

        $this->test_field_types_registry();
        $this->test_text_field_render();
        $this->test_select_field_render();
        $this->test_dependency_evaluation();
        $this->test_value_resolution();
        
        // Тесты для новых типов (v2.2)
        $this->test_accordion_field_render();
        $this->test_tabbed_field_render();
        $this->test_typography_field_render();
        $this->test_spacing_field_render();
        $this->test_dimensions_field_render();
        $this->test_border_field_render();
        $this->test_background_field_render();
        $this->test_link_color_field_render();
        $this->test_color_group_field_render();
        $this->test_image_select_field_render();

        echo "\n=== Results ===\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n";
    }

    private function test_field_types_registry() {
        echo "Test: Field types registry initialization\n";

        WP_Field::init_field_types();
        $reflection = new ReflectionClass('WP_Field');
        $property = $reflection->getProperty('field_types');
        $property->setAccessible(true);
        $types = $property->getValue();

        if (!empty($types) && isset($types['text']) && isset($types['select'])) {
            echo "✓ Field types registry initialized correctly\n";
            $this->passed++;
        } else {
            echo "✗ Field types registry failed\n";
            $this->failed++;
        }
    }

    private function test_text_field_render() {
        echo "Test: Text field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_text',
            'type'  => 'text',
            'label' => 'Test Text',
        ], false);

        if (strpos($html, 'wp-field') !== false && strpos($html, 'test_text') !== false) {
            echo "✓ Text field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Text field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_select_field_render() {
        echo "Test: Select field rendering\n";

        $html = WP_Field::make([
            'id'      => 'test_select',
            'type'    => 'select',
            'label'   => 'Test Select',
            'options' => ['a' => 'Option A', 'b' => 'Option B'],
        ], false);

        if (strpos($html, '<select') !== false && strpos($html, 'Option A') !== false) {
            echo "✓ Select field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Select field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_dependency_evaluation() {
        echo "Test: Dependency evaluation\n";

        $field = new WP_Field([
            'id'    => 'test_dep',
            'type'  => 'text',
            'label' => 'Test Dependency',
            'dependency' => [
                ['other_field', '==', 'value'],
            ],
        ], 'options');

        // Проверяем, что метод существует и работает
        $reflection = new ReflectionClass($field);
        $method = $reflection->getMethod('is_field_hidden');
        $method->setAccessible(true);

        // Без значения в БД, поле должно быть скрыто
        $is_hidden = $method->invoke($field);

        if ($is_hidden === true) {
            echo "✓ Dependency evaluation works correctly\n";
            $this->passed++;
        } else {
            echo "✗ Dependency evaluation failed\n";
            $this->failed++;
        }
    }

    private function test_value_resolution() {
        echo "Test: Value resolution\n";

        $field = new WP_Field([
            'id'      => 'test_value',
            'type'    => 'text',
            'label'   => 'Test Value',
            'value'   => 'explicit_value',
            'default' => 'default_value',
        ], 'options');

        $reflection = new ReflectionClass($field);
        $method = $reflection->getMethod('get_field_value');
        $method->setAccessible(true);

        $value = $method->invoke($field, $field->field);

        if ($value === 'explicit_value') {
            echo "✓ Value resolution works correctly\n";
            $this->passed++;
        } else {
            echo "✗ Value resolution failed (got: {$value})\n";
            $this->failed++;
        }
    }

    private function test_accordion_field_render() {
        echo "Test: Accordion field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_accordion',
            'type'  => 'accordion',
            'label' => 'Test Accordion',
            'sections' => [
                ['title' => 'Section 1', 'content' => 'Content 1'],
                ['title' => 'Section 2', 'content' => 'Content 2'],
            ],
        ], false);

        if (strpos($html, 'wp-field-accordion') !== false && strpos($html, 'Section 1') !== false) {
            echo "✓ Accordion field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Accordion field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_tabbed_field_render() {
        echo "Test: Tabbed field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_tabbed',
            'type'  => 'tabbed',
            'label' => 'Test Tabbed',
            'tabs' => [
                ['title' => 'Tab 1', 'content' => 'Content 1'],
                ['title' => 'Tab 2', 'content' => 'Content 2'],
            ],
        ], false);

        if (strpos($html, 'wp-field-tabbed') !== false && strpos($html, 'Tab 1') !== false) {
            echo "✓ Tabbed field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Tabbed field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_typography_field_render() {
        echo "Test: Typography field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_typography',
            'type'  => 'typography',
            'label' => 'Test Typography',
        ], false);

        if (strpos($html, 'wp-field-typography') !== false && strpos($html, 'Font Family') !== false) {
            echo "✓ Typography field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Typography field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_spacing_field_render() {
        echo "Test: Spacing field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_spacing',
            'type'  => 'spacing',
            'label' => 'Test Spacing',
        ], false);

        if (strpos($html, 'wp-field-spacing') !== false && strpos($html, 'wp-field-spacing-visual') !== false) {
            echo "✓ Spacing field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Spacing field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_dimensions_field_render() {
        echo "Test: Dimensions field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_dimensions',
            'type'  => 'dimensions',
            'label' => 'Test Dimensions',
        ], false);

        if (strpos($html, 'wp-field-dimensions') !== false && strpos($html, 'Width') !== false && strpos($html, 'Height') !== false) {
            echo "✓ Dimensions field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Dimensions field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_border_field_render() {
        echo "Test: Border field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_border',
            'type'  => 'border',
            'label' => 'Test Border',
        ], false);

        if (strpos($html, 'wp-field-border') !== false && strpos($html, 'Style') !== false && strpos($html, 'Width') !== false) {
            echo "✓ Border field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Border field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_background_field_render() {
        echo "Test: Background field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_background',
            'type'  => 'background',
            'label' => 'Test Background',
        ], false);

        if (strpos($html, 'wp-field-background') !== false && strpos($html, 'Background Color') !== false) {
            echo "✓ Background field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Background field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_link_color_field_render() {
        echo "Test: Link Color field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_link_color',
            'type'  => 'link_color',
            'label' => 'Test Link Color',
        ], false);

        if (strpos($html, 'wp-field-link-color') !== false && strpos($html, 'Normal') !== false && strpos($html, 'Hover') !== false) {
            echo "✓ Link Color field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Link Color field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_color_group_field_render() {
        echo "Test: Color Group field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_color_group',
            'type'  => 'color_group',
            'label' => 'Test Color Group',
        ], false);

        if (strpos($html, 'wp-field-color-group') !== false && strpos($html, 'Primary') !== false) {
            echo "✓ Color Group field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Color Group field rendering failed\n";
            $this->failed++;
        }
    }

    private function test_image_select_field_render() {
        echo "Test: Image Select field rendering\n";

        $html = WP_Field::make([
            'id'    => 'test_image_select',
            'type'  => 'image_select',
            'label' => 'Test Image Select',
            'options' => [
                'opt1' => ['src' => 'http://example.com/img1.jpg', 'label' => 'Option 1'],
                'opt2' => ['src' => 'http://example.com/img2.jpg', 'label' => 'Option 2'],
            ],
        ], false);

        if (strpos($html, 'wp-field-image-select') !== false && strpos($html, 'Option 1') !== false) {
            echo "✓ Image Select field rendered correctly\n";
            $this->passed++;
        } else {
            echo "✗ Image Select field rendering failed\n";
            $this->failed++;
        }
    }
}

// Запуск тестов
$tests = new WP_Field_Tests();
$tests->run();
