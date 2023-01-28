# wp-field
Universal simple generator of HTML fields for developer. Library for WordPress. 
You can use it for any parts in WordPress: post metaboxes, profile pages, options page and something.

It will save you time, does not burden the WordPress and is very simple!!!

## How start

1. Upload it as WordPress plugin or as dependency
2. Create new field: 

 `new WP_Field(['id'=>'text_field','type'=>'text','label'=>'Label'], 'option', 'option_name');`

4. View result https://i.imgur.com/85ABxQR.png

## How to use with params
`new WP_Field( array params, string type, string db_name);`

Where `params`: 
<pre>
[
    'id'   => 'option_name' required,
    'type' => 'select', 'text', 'textarea', 'checkbox' etc. text by default,
    'label' => 'string',
    'placeholder' => 'string',
    'class' => 'string',
    'custom_attributes' => [],
    'val'   => 'default value (slug: value, default)'
]
</pre>

**id, type, label(title) - Required params !!!**

`type`: 'option', 'post', 'comment', 'term', 'user', or any other object type with an associated meta table.

`db_name`: option name in DB for getting value

## Examples:

### Text field:
https://i.imgur.com/85ABxQR.png

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

### Select:
https://i.imgur.com/BCONXoM.png

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

### Checkbox:
https://i.imgur.com/afquc1H.png

<pre>
new WP_Field([
   'id'    => 'site_checkbox',
   'type'  => 'checkbox',
   'title' => 'Enable it?',
], 'option', 'option_name');
</pre>

### Textarea:

<pre>
new WP_Field([
   'id'    => 'site_description',
   'type'  => 'textarea',
   'title' => 'Description',
], 'option', 'option_name');
</pre>

### Number (int):

<pre>
new WP_Field([
   'id'    => 'site_number',
   'type'  => 'number',
   'title' => 'Enter number...',
], 'option', 'option_name');
</pre>

### Radio:

<pre>
new WP_Field([
    'id'    => 'site_radio',
    'type'  => 'radio',
    'placeholder' => 'Choose something..',
    'label'       => __('Please choose something..', 'wp'),
    'options' => ['value_1' => 'Option name 1', 'value_2' => 'Option name 2',]
], 'option', 'option_name');
</pre>

### WP Editor:

<pre>
new WP_Field([
   'id'    => 'site_editor',
   'type'  => 'editor',
   'title' => 'HTML WP Editor',
], 'option', 'option_name');
</pre>

### Media file (wp lib):

<pre>
new WP_Field([
    'id'    => 'site_media',
    'type'  => 'media',
    'title' => 'Select media file',
], 'option', 'option_name');
</pre>


### Date and time:

<pre>
new WP_Field([
    'id'    => 'site_datetime',
    'type'  => 'datetime',
    'title' => 'Time field',
], 'option', 'option_name');
</pre>

### Image picker:

<pre>
new WP_Field([
    'id'    => 'site_imagepicker',
    'type'  => 'imagepicker',
    'title' => 'Pick image',
    'options' => [
        '1' => 'http://placekitten.com/220/200',
        '2' => 'http://placekitten.com/180/200',
    ],
], 'option', 'option_name');
</pre>


## Example. Use post meta as field value
Where 
'meta_name' = post meta key, 
'post' = for any post type
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

