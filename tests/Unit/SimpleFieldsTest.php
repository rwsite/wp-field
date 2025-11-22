<?php

/**
 * Pest тесты для 9 простых типов полей (v2.1)
 * Switcher, Spinner, Button Set, Slider, Heading, Subheading, Notice, Content, Fieldset
 */

use PHPUnit\Framework\TestCase;

// Загружаем класс WP_Field
require_once dirname(__DIR__, 2) . '/WP_Field.php';

describe('Simple Fields v2.1', function () {
    
    // ===== SWITCHER =====
    describe('Switcher Field', function () {
        
        it('should render switcher with default parameters', function () {
            $html = WP_Field::make([
                ['id'    => 'test_switcher',
                'type'  => 'switcher',
                'label' => 'Enable Feature'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-switcher');
            expect($html)->toContain('type="checkbox"');
            expect($html)->toContain('value="1"');
            expect($html)->toContain('On');
            expect($html)->toContain('Off');
        });

        it('should render switcher with custom text', function () {
            $html = WP_Field::make([
                ['id'        => 'test_switcher',
                'type'      => 'switcher',
                'label'     => 'Enable',
                'text_on'   => 'Yes',
                'text_off'  => 'No'],
                'options'
            ], false);

            expect($html)->toContain('Yes');
            expect($html)->toContain('No');
        });

        it('should render switcher with checked state', function () {
            $html = WP_Field::make([
                ['id'      => 'test_switcher',
                'type'    => 'switcher',
                'label'   => 'Enable',
                'value'   => '1'],
                'options'
            ], false);

            expect($html)->toContain('checked');
        });

        it('should render switcher with description', function () {
            $html = WP_Field::make([
                ['id'    => 'test_switcher',
                'type'  => 'switcher',
                'label' => 'Enable',
                'desc'  => 'Turn on/off this feature'],
                'options'
            ], false);

            expect($html)->toContain('Turn on/off this feature');
            expect($html)->toContain('description');
        });
    });

    // ===== SPINNER =====
    describe('Spinner Field', function () {
        
        it('should render spinner with default parameters', function () {
            $html = WP_Field::make([
                ['id'    => 'test_spinner',
                'type'  => 'spinner',
                'label' => 'Quantity'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-spinner');
            expect($html)->toContain('wp-field-spinner-up');
            expect($html)->toContain('wp-field-spinner-down');
            expect($html)->toContain('type="number"');
        });

        it('should render spinner with min/max/step', function () {
            $html = WP_Field::make([
                ['id'    => 'test_spinner',
                'type'  => 'spinner',
                'label' => 'Quantity',
                'min'   => 1,
                'max'   => 100,
                'step'  => 5],
                'options'
            ], false);

            expect($html)->toContain('min="1"');
            expect($html)->toContain('max="100"');
            expect($html)->toContain('step="5"');
        });

        it('should render spinner with unit', function () {
            $html = WP_Field::make([
                ['id'    => 'test_spinner',
                'type'  => 'spinner',
                'label' => 'Weight',
                'unit'  => 'kg'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-spinner-unit');
            expect($html)->toContain('kg');
        });

        it('should render spinner with value', function () {
            $html = WP_Field::make([
                ['id'    => 'test_spinner',
                'type'  => 'spinner',
                'label' => 'Quantity',
                'value' => '42'],
                'options'
            ], false);

            expect($html)->toContain('value="42"');
        });
    });

    // ===== BUTTON SET =====
    describe('Button Set Field', function () {
        
        it('should render button set with radio buttons', function () {
            $html = WP_Field::make([
                ['id'      => 'test_button_set',
                'type'    => 'button_set',
                'label'   => 'Alignment',
                'options' => [
                    'left'   => 'Left',
                    'center' => 'Center',
                    'right'  => 'Right',
                ]],
                'options'
            ], false);

            expect($html)->toContain('wp-field-button-set');
            expect($html)->toContain('type="radio"');
            expect($html)->toContain('Left');
            expect($html)->toContain('Center');
            expect($html)->toContain('Right');
        });

        it('should render button set with checkboxes (multiple)', function () {
            $html = WP_Field::make([
                ['id'       => 'test_button_set',
                'type'     => 'button_set',
                'label'    => 'Options',
                'multiple' => true,
                'options'  => [
                    'opt1' => 'Option 1',
                    'opt2' => 'Option 2',
                ]],
                'options'
            ], false);

            expect($html)->toContain('type="checkbox"');
            expect($html)->toContain('name="test_button_set[]"');
        });

        it('should render button set with selected value', function () {
            $html = WP_Field::make([
                ['id'      => 'test_button_set',
                'type'    => 'button_set',
                'label'   => 'Alignment',
                'value'   => 'center',
                'options' => [
                    'left'   => 'Left',
                    'center' => 'Center',
                    'right'  => 'Right',
                ]],
                'options'
            ], false);

            expect($html)->toContain('value="center"');
            expect($html)->toContain('checked');
        });

        it('should render button set without options', function () {
            $html = WP_Field::make([
                ['id'    => 'test_button_set',
                'type'  => 'button_set',
                'label' => 'Options'],
                'options'
            ], false);

            expect($html)->toContain('No options provided');
        });
    });

    // ===== SLIDER =====
    describe('Slider Field', function () {
        
        it('should render slider with default parameters', function () {
            $html = WP_Field::make([
                ['id'    => 'test_slider',
                'type'  => 'slider',
                'label' => 'Volume'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-slider-wrapper');
            expect($html)->toContain('type="range"');
            expect($html)->toContain('wp-field-slider');
        });

        it('should render slider with min/max/step', function () {
            $html = WP_Field::make([
                ['id'    => 'test_slider',
                'type'  => 'slider',
                'label' => 'Volume',
                'min'   => 0,
                'max'   => 100,
                'step'  => 10],
                'options'
            ], false);

            expect($html)->toContain('min="0"');
            expect($html)->toContain('max="100"');
            expect($html)->toContain('step="10"');
        });

        it('should render slider with value display', function () {
            $html = WP_Field::make([
                ['id'         => 'test_slider',
                'type'       => 'slider',
                'label'      => 'Volume',
                'value'      => '50',
                'show_value' => true],
                'options'
            ], false);

            expect($html)->toContain('wp-field-slider-value');
            expect($html)->toContain('50');
        });

        it('should render slider with value', function () {
            $html = WP_Field::make([
                ['id'    => 'test_slider',
                'type'  => 'slider',
                'label' => 'Volume',
                'value' => '75'],
                'options'
            ], false);

            expect($html)->toContain('value="75"');
        });
    });

    // ===== HEADING =====
    describe('Heading Field', function () {
        
        it('should render heading with default tag', function () {
            $html = WP_Field::make([
                ['id'    => 'test_heading',
                'type'  => 'heading',
                'label' => 'Section Title'],
                'options'
            ], false);

            expect($html)->toContain('<h3');
            expect($html)->toContain('Section Title');
            expect($html)->toContain('</h3>');
            expect($html)->toContain('wp-field-heading');
        });

        it('should render heading with custom tag', function () {
            $html = WP_Field::make([
                ['id'    => 'test_heading',
                'type'  => 'heading',
                'label' => 'Title',
                'tag'   => 'h2'],
                'options'
            ], false);

            expect($html)->toContain('<h2');
            expect($html)->toContain('</h2>');
        });

        it('should render heading with custom class', function () {
            $html = WP_Field::make([
                ['id'    => 'test_heading',
                'type'  => 'heading',
                'label' => 'Title',
                'class' => 'custom-heading'],
                'options'
            ], false);

            expect($html)->toContain('custom-heading');
        });
    });

    // ===== SUBHEADING =====
    describe('Subheading Field', function () {
        
        it('should render subheading with default tag', function () {
            $html = WP_Field::make([
                ['id'    => 'test_subheading',
                'type'  => 'subheading',
                'label' => 'Subsection'],
                'options'
            ], false);

            expect($html)->toContain('<h4');
            expect($html)->toContain('Subsection');
            expect($html)->toContain('</h4>');
            expect($html)->toContain('wp-field-subheading');
        });

        it('should render subheading with custom tag', function () {
            $html = WP_Field::make([
                ['id'    => 'test_subheading',
                'type'  => 'subheading',
                'label' => 'Subtitle',
                'tag'   => 'h3'],
                'options'
            ], false);

            expect($html)->toContain('<h3');
            expect($html)->toContain('</h3>');
        });
    });

    // ===== NOTICE =====
    describe('Notice Field', function () {
        
        it('should render notice with default type', function () {
            $html = WP_Field::make([
                ['id'    => 'test_notice',
                'type'  => 'notice',
                'label' => 'This is an info message'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-notice');
            expect($html)->toContain('wp-field-notice-info');
            expect($html)->toContain('This is an info message');
        });

        it('should render notice with success type', function () {
            $html = WP_Field::make([
                ['id'           => 'test_notice',
                'type'         => 'notice',
                'label'        => 'Success!',
                'type_notice'  => 'success'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-notice-success');
            expect($html)->toContain('Success!');
        });

        it('should render notice with warning type', function () {
            $html = WP_Field::make([
                ['id'           => 'test_notice',
                'type'         => 'notice',
                'label'        => 'Warning!',
                'notice_type'  => 'warning'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-notice-warning');
            expect($html)->toContain('Warning!');
        });

        it('should render notice with error type', function () {
            $html = WP_Field::make([
                ['id'           => 'test_notice',
                'type'         => 'notice',
                'label'        => 'Error!',
                'type_notice'  => 'error'],
                'options'
            ], false);

            expect($html)->toContain('wp-field-notice-error');
            expect($html)->toContain('Error!');
        });
    });

    // ===== CONTENT =====
    describe('Content Field', function () {
        
        it('should render content as HTML', function () {
            $html = WP_Field::make([
                ['id'    => 'test_content',
                'type'  => 'content',
                'label' => '<p>This is <strong>HTML</strong> content</p>'],
                'options'
            ], false);

            expect($html)->toContain('<p>This is <strong>HTML</strong> content</p>');
        });

        it('should render content with empty label', function () {
            $html = WP_Field::make([
                ['id'    => 'test_content',
                'type'  => 'content',
                'label' => ''],
                'options'
            ], false);

            expect($html)->toContain('wp-field-content');
        });
    });

    // ===== FIELDSET =====
    describe('Fieldset Field', function () {
        
        it('should render fieldset with legend', function () {
            $html = WP_Field::make([
                ['id'     => 'test_fieldset',
                'type'   => 'fieldset',
                'label'  => 'Personal Information',
                'fields' => []],
                'options'
            ], false);

            expect($html)->toContain('<fieldset');
            expect($html)->toContain('wp-field-fieldset');
            expect($html)->toContain('<legend>Personal Information</legend>');
            expect($html)->toContain('</fieldset>');
        });

        it('should render fieldset with custom class', function () {
            $html = WP_Field::make([
                ['id'    => 'test_fieldset',
                'type'  => 'fieldset',
                'label' => 'Group',
                'class' => 'custom-fieldset',
                'fields' => []],
                'options'
            ], false);

            expect($html)->toContain('custom-fieldset');
        });

        it('should render fieldset with legend parameter', function () {
            $html = WP_Field::make([
                ['id'     => 'test_fieldset',
                'type'   => 'fieldset',
                'label'  => '',
                'legend' => 'Custom Legend',
                'fields' => []],
                'options'
            ], false);

            expect($html)->toContain('<legend>Custom Legend</legend>');
        });
    });

    // ===== REGISTRATION IN REGISTRY =====
    describe('Field Types Registry', function () {
        
        it('should have all 9 new field types registered', function () {
            WP_Field::init_field_types();
            $reflection = new ReflectionClass('WP_Field');
            $property = $reflection->getProperty('field_types');
            $property->setAccessible(true);
            $types = $property->getValue();

            expect(isset($types['switcher']))->toBeTrue();
            expect(isset($types['spinner']))->toBeTrue();
            expect(isset($types['button_set']))->toBeTrue();
            expect(isset($types['slider']))->toBeTrue();
            expect(isset($types['heading']))->toBeTrue();
            expect(isset($types['subheading']))->toBeTrue();
            expect(isset($types['notice']))->toBeTrue();
            expect(isset($types['content']))->toBeTrue();
            expect(isset($types['fieldset']))->toBeTrue();
        });

        it('should have correct render methods for all types', function () {
            WP_Field::init_field_types();
            $reflection = new ReflectionClass('WP_Field');
            $property = $reflection->getProperty('field_types');
            $property->setAccessible(true);
            $types = $property->getValue();

            expect($types['switcher'][0])->toBe('render_switcher');
            expect($types['spinner'][0])->toBe('render_spinner');
            expect($types['button_set'][0])->toBe('render_button_set');
            expect($types['slider'][0])->toBe('render_slider');
            expect($types['heading'][0])->toBe('render_heading');
            expect($types['subheading'][0])->toBe('render_subheading');
            expect($types['notice'][0])->toBe('render_notice');
            expect($types['content'][0])->toBe('render_content');
            expect($types['fieldset'][0])->toBe('render_fieldset');
        });
    });

    // ===== COMMON ATTRIBUTES =====
    describe('Common Field Attributes', function () {
        
        it('should render field with custom attributes', function () {
            $html = WP_Field::make([
                ['id'                 => 'test_field',
                'type'               => 'spinner',
                'label'              => 'Test',
                'custom_attributes'  => [
                    'data-test' => 'value',
                    'aria-label' => 'Test Field',
                ]],
                'options'
            ], false);

            expect($html)->toContain('data-test="value"');
            expect($html)->toContain('aria-label="Test Field"');
        });

        it('should render field with readonly attribute', function () {
            $html = WP_Field::make([
                ['id'       => 'test_field',
                'type'     => 'spinner',
                'label'    => 'Test',
                'readonly' => true],
                'options'
            ], false);

            expect($html)->toContain('readonly');
        });

        it('should render field with disabled attribute', function () {
            $html = WP_Field::make([
                ['id'       => 'test_field',
                'type'     => 'spinner',
                'label'    => 'Test',
                'disabled' => true],
                'options'
            ], false);

            expect($html)->toContain('disabled');
        });
    });

    // ===== WRAPPER AND STRUCTURE =====
    describe('Field Wrapper Structure', function () {
        
        it('should wrap field in wp-field div', function () {
            $html = WP_Field::make([
                ['id'    => 'test_field',
                'type'  => 'switcher',
                'label' => 'Test'],
                'options'
            ], false);

            expect($html)->toContain('class="wp-field wp-field-switcher"');
            expect($html)->toContain('data-field-id="test_field"');
            expect($html)->toContain('data-field-type="switcher"');
        });

        it('should include field ID in wrapper', function () {
            $html = WP_Field::make([
                ['id'    => 'my_custom_id',
                'type'  => 'spinner',
                'label' => 'Test'],
                'options'
            ], false);

            expect($html)->toContain('data-field-id="my_custom_id"');
        });
    });
});
