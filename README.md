<p align="center">
  <img src="placeholder.svg" alt="WP_Field Logo" width="150" height="150">
</p>

<h1 align="center">WP_Field</h1>

<p align="center">
  <strong>Universal HTML Field Generator for WordPress</strong><br>
  Minimalist, extensible library for creating fields in WordPress with support for:<br>
  52 field types, dependency system, all storage types, and built-in WP components.
</p>

<p align="center">
  <a href="https://packagist.org/packages/rwsite/wp-field"><img src="https://img.shields.io/packagist/v/rwsite/wp-field.svg?style=flat-square" alt="Latest Version"></a>
  <img src="https://img.shields.io/badge/PHP-8.0+-blue.svg?style=flat-square" alt="PHP Version">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg?style=flat-square" alt="License"></a>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#quick-start">Quick Start</a> •
  <a href="#field-types">Field Types</a> •
  <a href="#examples">Examples</a> •
  <a href="#dependencies">Dependencies</a> •
  <a href="README.ru.md">RU version</a>
</p>

---

## Features

- 🚀 **52 Field Types** — Basic, choice, advanced, composite, and specialized fields
- 🔗 **Dependency System** — 12 operators with AND/OR logic for field visibility
- 📦 **Multiple Storages** — Post meta, options, term meta, user meta, comment meta
- 🎨 **WP Components** — wp_editor, wp-color-picker, wp.media, CodeMirror integration
- 🔌 **Zero Dependencies** — Uses only built-in WordPress scripts and components
- 🌍 **i18n Ready** — Translations included (Russian)
- 📊 **Interactive Demo** — Live examples page in WordPress admin

## Requirements

- PHP 8.0+
- WordPress 4.6+

## Installation

1. Clone or download to `wp-content/plugins/wp-field`
2. Run `composer install`
3. Activate the plugin

## Quick Start

### Simple Text Field

```php
// Simple text field
WP_Field::make([
    'id'    => 'shop_name',
    'type'  => 'text',
    'label' => 'Shop Name',
]);

// Select with dependency
WP_Field::make([
    'id'      => 'delivery_type',
    'type'    => 'select',
    'label'   => 'Delivery Type',
    'options' => ['courier' => 'Courier', 'pickup' => 'Pickup'],
]);

WP_Field::make([
    'id'    => 'delivery_address',
    'type'  => 'text',
    'label' => 'Delivery Address',
    'dependency' => [
        ['delivery_type', '==', 'courier'],
    ],
]);
```

### Dispatching Fields

```php
use WP_Field\WP_Field;

// Dispatch to output
WP_Field::make($field_config, true, 'post', $post_id);

// Save to options
WP_Field::make($field_config, false, 'options');

// Term meta
WP_Field::make($field_config, false, 'term', $term_id);

// User meta
WP_Field::make($field_config, false, 'user', $user_id);

// Comment meta
WP_Field::make($field_config, false, 'comment', $comment_id);
```

## Field Types (52)

### Basic (9)
- `text` — Text input
- `password` — Password field
- `email` — Email input
- `url` — URL input
- `tel` — Telephone input
- `number` — Number input
- `range` — Range slider
- `hidden` — Hidden field
- `textarea` — Multi-line text

### Choice (5)
- `select` — Dropdown list
- `multiselect` — Multiple selection
- `radio` — Radio buttons
- `checkbox` — Single checkbox
- `checkbox_group` — Checkbox group

### Advanced (9)
- `editor` — wp_editor
- `media` — Media library (ID or URL)
- `image` — Image with preview
- `file` — File upload
- `gallery` — Image gallery
- `color` — Color picker
- `date` — Date picker
- `time` — Time picker
- `datetime` — Date and time

### Composite (2)
- `group` — Nested fields
- `repeater` — Repeating elements

### Simple v2.1 (9)
- `switcher` — On/off switcher
- `spinner` — Number spinner
- `button_set` — Button selection
- `slider` — Value slider
- `heading` — Heading
- `subheading` — Subheading
- `notice` — Notice (info/success/warning/error)
- `content` — Custom HTML content
- `fieldset` — Field grouping

### Medium Complexity v2.2 (10)
- `accordion` — Collapsible sections
- `tabbed` — Tabs
- `typography` — Typography settings
- `spacing` — Spacing controls
- `dimensions` — Size controls
- `border` — Border settings
- `background` — Background options
- `link_color` — Link colors
- `color_group` — Color group
- `image_select` — Image selection

### High Complexity v2.3 (8)
- `code_editor` — Code editor with syntax highlighting
- `icon` — Icon picker from library
- `map` — Google Maps location
- `sortable` — Drag & drop sorting
- `sorter` — Enabled/disabled sorting
- `palette` — Color palette
- `link` — Link field (URL + text + target)
- `backup` — Settings export/import

## Examples

### Dependencies

```php
// Show field only if another field has specific value
WP_Field::make([
    'id'    => 'courier_address',
    'type'  => 'text',
    'label' => 'Delivery Address',
    'dependency' => [
        ['delivery_type', '==', 'courier'],
    ],
]);

// Multiple conditions (AND)
WP_Field::make([
    'id'    => 'special_field',
    'type'  => 'text',
    'label' => 'Special Field',
    'dependency' => [
        ['field1', '==', 'value1'],
        ['field2', '!=', 'value2'],
        'relation' => 'AND',
    ],
]);

// Multiple conditions (OR)
WP_Field::make([
    'id'    => 'notification',
    'type'  => 'text',
    'label' => 'Notification',
    'dependency' => [
        ['type', '==', 'sms'],
        ['type', '==', 'email'],
        'relation' => 'OR',
    ],
]);
```

### Repeater

```php
WP_Field::make([
    'id'       => 'work_times',
    'type'     => 'repeater',
    'label'    => 'Work Times',
    'min'      => 1,
    'max'      => 7,
    'add_text' => 'Add Day',
    'fields'   => [
        [
            'id'      => 'day',
            'type'    => 'select',
            'label'   => 'Day',
            'options' => ['mon' => 'Mon', 'tue' => 'Tue'],
        ],
        [
            'id'    => 'from',
            'type'  => 'time',
            'label' => 'From',
        ],
        [
            'id'    => 'to',
            'type'  => 'time',
            'label' => 'To',
        ],
    ],
]);
```

### Group

```php
WP_Field::make([
    'id'    => 'address',
    'type'  => 'group',
    'label' => 'Address',
    'fields' => [
        ['id' => 'city', 'type' => 'text', 'label' => 'City'],
        ['id' => 'street', 'type' => 'text', 'label' => 'Street'],
        ['id' => 'number', 'type' => 'text', 'label' => 'Number'],
    ],
]);
```

### Code Editor (v2.3)

```php
WP_Field::make([
    'id'     => 'custom_css',
    'type'   => 'code_editor',
    'label'  => 'Custom CSS',
    'mode'   => 'css', // css, javascript, php, html
    'height' => '400px',
]);
```

### Icon Picker (v2.3)

```php
WP_Field::make([
    'id'      => 'menu_icon',
    'type'    => 'icon',
    'label'   => 'Menu Icon',
    'library' => 'dashicons',
]);
```

### Map (v2.3)

```php
WP_Field::make([
    'id'      => 'location',
    'type'    => 'map',
    'label'   => 'Location',
    'api_key' => 'YOUR_GOOGLE_MAPS_API_KEY',
    'zoom'    => 12,
    'center'  => ['lat' => 55.7558, 'lng' => 37.6173],
]);
```

### Sortable (v2.3)

```php
WP_Field::make([
    'id'      => 'menu_order',
    'type'    => 'sortable',
    'label'   => 'Menu Order',
    'options' => [
        'home'     => 'Home',
        'about'    => 'About',
        'services' => 'Services',
        'contact'  => 'Contact',
    ],
]);
```

### Palette (v2.3)

```php
WP_Field::make([
    'id'       => 'color_scheme',
    'type'     => 'palette',
    'label'    => 'Color Scheme',
    'palettes' => [
        'blue'   => ['#0073aa', '#005a87', '#003d82'],
        'green'  => ['#28a745', '#218838', '#1e7e34'],
        'red'    => ['#dc3545', '#c82333', '#bd2130'],
    ],
]);
```

### Link (v2.3)

```php
WP_Field::make([
    'id'    => 'cta_button',
    'type'  => 'link',
    'label' => 'CTA Button',
]);

// Get value:
$link = get_post_meta($post_id, 'cta_button', true);
// ['url' => '...', 'text' => '...', 'target' => '_blank']
```

### Accordion (v2.2)

```php
WP_Field::make([
    'id'       => 'settings_accordion',
    'type'     => 'accordion',
    'label'    => 'Settings',
    'sections' => [
        [
            'title'  => 'General',
            'open'   => true,
            'fields' => [
                ['id' => 'title', 'type' => 'text', 'label' => 'Title'],
            ],
        ],
        [
            'title'  => 'Advanced',
            'fields' => [
                ['id' => 'desc', 'type' => 'textarea', 'label' => 'Description'],
            ],
        ],
    ],
]);
```

### Typography (v2.2)

```php
WP_Field::make([
    'id'    => 'heading_typography',
    'type'  => 'typography',
    'label' => 'Heading Typography',
]);

// Saved as:
// [
//     'font_family' => 'Arial',
//     'font_size' => '24',
//     'font_weight' => '700',
//     'line_height' => '1.5',
//     'text_align' => 'center',
//     'color' => '#333333'
// ]
```

## Dependency Operators

- `==` — Equal
- `!=` — Not equal
- `>`, `>=`, `<`, `<=` — Comparison
- `in` — In array
- `not_in` — Not in array
- `contains` — Contains
- `not_contains` — Not contains
- `empty` — Empty
- `not_empty` — Not empty

## Interactive Demo

**See all 52 field types in action:**

👉 **Tools → WP_Field Examples**  
or  
👉 `/wp-admin/tools.php?page=wp-field-examples`

The page includes:
- ✅ All field types with live examples
- ✅ Code for each field
- ✅ Dependency system demonstration
- ✅ Ability to save and test

## Extensibility

### Adding Custom Field Types

```php
add_filter('wp_field_types', function($types) {
    $types['custom_type'] = ['render_custom', ['default' => 'value']];
    return $types;
});
```

### Adding Icon Libraries

```php
add_filter('wp_field_icon_library', function($icons, $library) {
    if ($library === 'fontawesome') {
        return ['fa-home', 'fa-user', 'fa-cog', ...];
    }
    return $icons;
}, 10, 2);
```

### Custom Value Retrieval

```php
add_filter('wp_field_get_value', function($value, $storage_type, $key, $id, $field) {
    if ($storage_type === 'custom') {
        return get_custom_value($key, $id);
    }
    return $value;
}, 10, 5);
```

## Changelog

See **[CHANGELOG.md](CHANGELOG.md)** for detailed version history.

## Project Stats

- **PHP Lines:** 2705 (WP_Field.php)
- **JS Lines:** 1222 (wp-field.js)
- **CSS Lines:** 1839 (wp-field.css)
- **Field Types:** 52+
- **Dependency Operators:** 12
- **Storage Types:** 5
- **External Dependencies:** 0

## Compatibility

- **WordPress:** 4.6+
- **PHP:** 7.4+
- **Dependencies:** jQuery, jQuery UI Sortable, WordPress built-in components
- **Browsers:** Chrome, Firefox, Safari, Edge (latest 2 versions)

## Performance

- Minimal CSS size: ~20KB
- Minimal JS size: ~15KB
- Lazy loading for heavy components (CodeMirror, Google Maps)
- Optimized selectors and events

## License

GPL v2 or later

## Author

Aleksei Tikhomirov (https://rwsite.ru)
