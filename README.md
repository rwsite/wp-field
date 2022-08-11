# wp-field
Universal generator of HTML fields. Library for WordPress.

`new WP_Field( array params, string type, string db_name);`

`params`: 
<pre>
[
'id'   => 'option_name' required,
'type' => 'select', 'text', 'textarea', 'checkbox' etc. text by default,
'placeholder' => 'string',
'label' => 'string',
'class' => 'string',
'custom_attributes' => [],
'val'   => 'default value (slug: value, default)'
]
</pre>

`type`: post, options, term, user, comment

`db_name`: option name in DB for getting value

## Examples:

### Select:
option
<pre>
new WP_Field([
    'id'          => 'option_name',
    'type'        => 'select',
    'placeholder' => 'Choose something..',
    'label'       => __('Please choose something..', 'wp'),
    'class'       => 'wp_field',
    'custom_attributes' => ['data' => 'data'],
    'multiple'    => true,
    'options'     => ['value_1' => 'Option name 1', 'value_2' => 'Option name 2',],
], 'option', 'option_name');
</pre>

Result: https://i.imgur.com/InOwHBN.png

### Text field:
type: option

<pre>
new WP_Field([
    'id'          => 'text',
    'type'        => 'text',
    'placeholder' => 'Type something..',
    'label'       => __('Please type something..', 'wp'),
    'class'       => '',
    'custom_attributes' => ['data' => 'data'],
    'default'     => 'Default value'
], 'option', 'option_name');
</pre>

Result:

---

type: post 

<pre>
new WP_Field([
    'id'          => 'text',
    'type'        => 'text',
    'placeholder' => 'Type something..',
    'label'       => __('Please type something..', 'wp'),
    'class'       => '',
    'custom_attributes' => ['data' => 'data'],
    'default'     => 'Default value'
], 'post', 'meta_name');
</pre>

