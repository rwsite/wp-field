<?php
/**
 * Простые тесты для WP_Field
 */

// Загружаем WordPress
require_once dirname(__DIR__, 5) . '/wp-load.php';

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
}

// Запуск тестов
$tests = new WP_Field_Tests();
$tests->run();
