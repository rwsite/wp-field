<?php
/**
 * Данные для примеров полей: аргументы и расширенные примеры
 * Подключается в example.php
 */

if (!defined('ABSPATH')) {
    exit;
}

return [
    // SELECT
    'select' => [
        'arguments' => [
            ['name' => 'options', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив опций [key => label]'],
            ['name' => 'placeholder', 'type' => 'string', 'default' => 'Выберите...', 'desc' => 'Первая пустая опция'],
        ],
        'advanced_examples' => [
            [
                'title' => 'С группировкой опций',
                'desc' => 'Использование optgroup для группировки',
                'code' => "WP_Field::make([\n    'id' => 'country',\n    'type' => 'select',\n    'label' => 'Страна',\n    'options' => [\n        'Европа' => [\n            'ru' => 'Россия',\n            'de' => 'Германия',\n            'fr' => 'Франция'\n        ],\n        'Азия' => [\n            'cn' => 'Китай',\n            'jp' => 'Япония'\n        ]\n    ]\n]);"
            ],
            [
                'title' => 'Динамические опции из БД',
                'desc' => 'Загрузка опций из пользовательских записей',
                'code' => "\$posts = get_posts(['post_type' => 'product', 'numberposts' => -1]);\n\$options = [];\nforeach (\$posts as \$post) {\n    \$options[\$post->ID] = \$post->post_title;\n}\n\nWP_Field::make([\n    'id' => 'related_product',\n    'type' => 'select',\n    'label' => 'Связанный товар',\n    'options' => \$options\n]);"
            ]
        ]
    ],
    
    // MEDIA
    'media' => [
        'arguments' => [
            ['name' => 'preview', 'type' => 'bool', 'default' => 'true', 'desc' => 'Показывать превью'],
            ['name' => 'url', 'type' => 'bool', 'default' => 'true', 'desc' => 'Показывать поле URL'],
            ['name' => 'button_text', 'type' => 'string', 'default' => 'Выбрать файл', 'desc' => 'Текст кнопки'],
            ['name' => 'library', 'type' => 'string', 'default' => 'all', 'desc' => 'Фильтр: image, video, audio, all'],
            ['name' => 'placeholder', 'type' => 'string', 'default' => '', 'desc' => 'Placeholder для URL'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Только изображения с превью',
                'desc' => 'Фильтр по типу файла и отображение превью',
                'code' => "WP_Field::make([\n    'id' => 'banner_image',\n    'type' => 'media',\n    'label' => 'Баннер',\n    'library' => 'image',\n    'preview' => true,\n    'button_text' => 'Выбрать баннер'\n]);"
            ],
            [
                'title' => 'Видео без превью',
                'desc' => 'Только URL поле для видео',
                'code' => "WP_Field::make([\n    'id' => 'promo_video',\n    'type' => 'media',\n    'label' => 'Промо видео',\n    'library' => 'video',\n    'preview' => false,\n    'placeholder' => 'URL видео...'\n]);"
            ],
            [
                'title' => 'Получение данных файла',
                'desc' => 'Получение полной информации о файле',
                'code' => "\$attachment_id = get_option('banner_image');\nif (\$attachment_id) {\n    \$url = wp_get_attachment_url(\$attachment_id);\n    \$title = get_the_title(\$attachment_id);\n    \$meta = wp_get_attachment_metadata(\$attachment_id);\n    \n    echo '<img src=\"' . \$url . '\" alt=\"' . \$title . '\">';\n}"
            ]
        ]
    ],
    
    // GALLERY
    'gallery' => [
        'arguments' => [
            ['name' => 'add_button', 'type' => 'string', 'default' => 'Добавить изображения', 'desc' => 'Текст кнопки добавления'],
            ['name' => 'edit_button', 'type' => 'string', 'default' => 'Редактировать', 'desc' => 'Текст кнопки редактирования'],
            ['name' => 'clear_button', 'type' => 'string', 'default' => 'Удалить все', 'desc' => 'Текст кнопки очистки'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Галерея товара',
                'desc' => 'Множественные изображения с сортировкой',
                'code' => "WP_Field::make([\n    'id' => 'product_gallery',\n    'type' => 'gallery',\n    'label' => 'Галерея товара',\n    'add_button' => 'Добавить фото',\n    'desc' => 'Перетащите для изменения порядка'\n]);"
            ],
            [
                'title' => 'Вывод галереи на фронтенде',
                'desc' => 'Получение и отображение всех изображений',
                'code' => "\$gallery_ids = get_post_meta(\$post_id, 'product_gallery', true);\nif (\$gallery_ids) {\n    \$ids = explode(',', \$gallery_ids);\n    echo '<div class=\"gallery\">';\n    foreach (\$ids as \$id) {\n        \$url = wp_get_attachment_image_url(\$id, 'large');\n        \$thumb = wp_get_attachment_image_url(\$id, 'thumbnail');\n        echo '<a href=\"' . \$url . '\">';\n        echo '<img src=\"' . \$thumb . '\" alt=\"\">';\n        echo '</a>';\n    }\n    echo '</div>';\n}"
            ]
        ]
    ],
    
    // COLOR
    'color' => [
        'arguments' => [
            ['name' => 'alpha', 'type' => 'bool', 'default' => 'false', 'desc' => 'Поддержка прозрачности (RGBA)'],
            ['name' => 'default', 'type' => 'string', 'default' => '#ffffff', 'desc' => 'Цвет по умолчанию'],
        ],
        'advanced_examples' => [
            [
                'title' => 'С поддержкой прозрачности',
                'desc' => 'RGBA формат для полупрозрачных цветов',
                'code' => "WP_Field::make([\n    'id' => 'overlay_color',\n    'type' => 'color',\n    'label' => 'Цвет оверлея',\n    'alpha' => true,\n    'default' => 'rgba(0, 0, 0, 0.5)'\n]);"
            ],
            [
                'title' => 'Использование в CSS',
                'desc' => 'Вывод цвета в inline стилях',
                'code' => "\$primary_color = get_option('primary_color', '#0073aa');\necho '<style>';\necho '.button { background: ' . \$primary_color . '; }';\necho '.link:hover { color: ' . \$primary_color . '; }';\necho '</style>';"
            ]
        ]
    ],
    
    // REPEATER
    'repeater' => [
        'arguments' => [
            ['name' => 'fields', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив повторяемых полей'],
            ['name' => 'button_text', 'type' => 'string', 'default' => 'Добавить элемент', 'desc' => 'Текст кнопки добавления'],
            ['name' => 'max', 'type' => 'int', 'default' => '—', 'desc' => 'Максимальное количество'],
            ['name' => 'min', 'type' => 'int', 'default' => '0', 'desc' => 'Минимальное количество'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Список преимуществ',
                'desc' => 'Повторяемые поля для списка преимуществ',
                'code' => "WP_Field::make([\n    'id' => 'features',\n    'type' => 'repeater',\n    'label' => 'Преимущества',\n    'button_text' => 'Добавить преимущество',\n    'fields' => [\n        [\n            'id' => 'icon',\n            'type' => 'icon',\n            'label' => 'Иконка'\n        ],\n        [\n            'id' => 'title',\n            'type' => 'text',\n            'label' => 'Заголовок'\n        ],\n        [\n            'id' => 'description',\n            'type' => 'textarea',\n            'label' => 'Описание',\n            'rows' => 3\n        ]\n    ]\n]);"
            ],
            [
                'title' => 'Вывод repeater на фронтенде',
                'desc' => 'Получение и отображение повторяемых данных',
                'code' => "\$features = get_option('features', []);\nif (!empty(\$features)) {\n    echo '<div class=\"features\">';\n    foreach (\$features as \$feature) {\n        echo '<div class=\"feature\">';\n        echo '<i class=\"' . \$feature['icon'] . '\"></i>';\n        echo '<h3>' . \$feature['title'] . '</h3>';\n        echo '<p>' . \$feature['description'] . '</p>';\n        echo '</div>';\n    }\n    echo '</div>';\n}"
            ]
        ]
    ],
    
    // SWITCHER
    'switcher' => [
        'arguments' => [
            ['name' => 'text_on', 'type' => 'string', 'default' => 'On', 'desc' => 'Текст для включенного состояния'],
            ['name' => 'text_off', 'type' => 'string', 'default' => 'Off', 'desc' => 'Текст для выключенного состояния'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Включение/выключение функции',
                'desc' => 'Простой переключатель с русскими текстами',
                'code' => "WP_Field::make([\n    'id' => 'enable_comments',\n    'type' => 'switcher',\n    'label' => 'Комментарии',\n    'text_on' => 'Включены',\n    'text_off' => 'Выключены'\n]);"
            ],
            [
                'title' => 'Проверка значения',
                'desc' => 'Использование switcher в условиях',
                'code' => "\$comments_enabled = get_option('enable_comments', '0');\nif (\$comments_enabled === '1') {\n    comments_template();\n} else {\n    echo '<p>Комментарии отключены</p>';\n}"
            ]
        ]
    ],
    
    // CODE_EDITOR
    'code_editor' => [
        'arguments' => [
            ['name' => 'mode', 'type' => 'string', 'default' => 'css', 'desc' => 'Режим: css, javascript, php, html'],
            ['name' => 'height', 'type' => 'string', 'default' => '300px', 'desc' => 'Высота редактора'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Пользовательский CSS',
                'desc' => 'Редактор для кастомных стилей',
                'code' => "WP_Field::make([\n    'id' => 'custom_css',\n    'type' => 'code_editor',\n    'label' => 'Дополнительный CSS',\n    'mode' => 'css',\n    'height' => '400px'\n]);"
            ],
            [
                'title' => 'Вывод кода в head',
                'desc' => 'Добавление кастомного CSS в head',
                'code' => "add_action('wp_head', function() {\n    \$custom_css = get_option('custom_css');\n    if (\$custom_css) {\n        echo '<style>' . \$custom_css . '</style>';\n    }\n});"
            ]
        ]
    ],
    
    // TYPOGRAPHY
    'typography' => [
        'arguments' => [],
        'advanced_examples' => [
            [
                'title' => 'Настройки шрифта заголовков',
                'desc' => 'Полный контроль над типографикой',
                'code' => "WP_Field::make([\n    'id' => 'heading_typography',\n    'type' => 'typography',\n    'label' => 'Типографика заголовков'\n]);"
            ],
            [
                'title' => 'Применение настроек',
                'desc' => 'Генерация CSS из настроек типографики',
                'code' => "\$typo = get_option('heading_typography', []);\nif (!empty(\$typo)) {\n    echo '<style>';\n    echo 'h1, h2, h3 {';\n    if (!empty(\$typo['font_family'])) {\n        echo 'font-family: ' . \$typo['font_family'] . ';';\n    }\n    if (!empty(\$typo['font_size'])) {\n        echo 'font-size: ' . \$typo['font_size'] . 'px;';\n    }\n    if (!empty(\$typo['font_weight'])) {\n        echo 'font-weight: ' . \$typo['font_weight'] . ';';\n    }\n    if (!empty(\$typo['color'])) {\n        echo 'color: ' . \$typo['color'] . ';';\n    }\n    echo '}';\n    echo '</style>';\n}"
            ]
        ]
    ],
    
    // SPACING
    'spacing' => [
        'arguments' => [
            ['name' => 'spacing_type', 'type' => 'string', 'default' => 'margin', 'desc' => 'Тип: margin или padding'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Отступы секции',
                'desc' => 'Настройка padding для секции',
                'code' => "WP_Field::make([\n    'id' => 'section_padding',\n    'type' => 'spacing',\n    'label' => 'Внутренние отступы',\n    'spacing_type' => 'padding'\n]);"
            ],
            [
                'title' => 'Применение отступов',
                'desc' => 'Генерация CSS из настроек spacing',
                'code' => "\$spacing = get_option('section_padding', []);\nif (!empty(\$spacing)) {\n    \$unit = \$spacing['unit'] ?? 'px';\n    echo '<style>';\n    echo '.section {';\n    echo 'padding-top: ' . (\$spacing['top'] ?? 0) . \$unit . ';';\n    echo 'padding-right: ' . (\$spacing['right'] ?? 0) . \$unit . ';';\n    echo 'padding-bottom: ' . (\$spacing['bottom'] ?? 0) . \$unit . ';';\n    echo 'padding-left: ' . (\$spacing['left'] ?? 0) . \$unit . ';';\n    echo '}';\n    echo '</style>';\n}"
            ]
        ]
    ],
    
    // IMAGE_SELECT
    'image_select' => [
        'arguments' => [
            ['name' => 'options', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив опций [key => url] или [key => [src, label]]'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Выбор макета страницы',
                'desc' => 'Визуальный выбор из нескольких макетов',
                'code' => "WP_Field::make([\n    'id' => 'page_layout',\n    'type' => 'image_select',\n    'label' => 'Макет страницы',\n    'options' => [\n        'sidebar-left' => [\n            'src' => get_template_directory_uri() . '/images/layout-left.png',\n            'label' => 'Сайдбар слева'\n        ],\n        'sidebar-right' => [\n            'src' => get_template_directory_uri() . '/images/layout-right.png',\n            'label' => 'Сайдбар справа'\n        ],\n        'no-sidebar' => [\n            'src' => get_template_directory_uri() . '/images/layout-full.png',\n            'label' => 'Без сайдбара'\n        ]\n    ]\n]);"
            ],
            [
                'title' => 'С SVG изображениями',
                'desc' => 'Использование inline SVG через data URI',
                'code' => "WP_Field::make([\n    'id' => 'theme_style',\n    'type' => 'image_select',\n    'label' => 'Стиль темы',\n    'options' => [\n        'light' => [\n            'src' => 'data:image/svg+xml;base64,' . base64_encode('<svg>...</svg>'),\n            'label' => 'Светлая'\n        ],\n        'dark' => [\n            'src' => 'data:image/svg+xml;base64,' . base64_encode('<svg>...</svg>'),\n            'label' => 'Тёмная'\n        ]\n    ]\n]);"
            ]
        ]
    ],
    
    // TEXTAREA
    'textarea' => [
        'arguments' => [
            ['name' => 'rows', 'type' => 'int', 'default' => '5', 'desc' => 'Количество строк'],
            ['name' => 'cols', 'type' => 'int', 'default' => '—', 'desc' => 'Количество колонок'],
            ['name' => 'placeholder', 'type' => 'string', 'default' => '', 'desc' => 'Текст-подсказка'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Большое текстовое поле',
                'desc' => 'Для длинных описаний',
                'code' => "WP_Field::make([\n    'id' => 'product_description',\n    'type' => 'textarea',\n    'label' => 'Описание товара',\n    'rows' => 10,\n    'placeholder' => 'Подробное описание...'\n]);"
            ]
        ]
    ],
    
    // NUMBER
    'number' => [
        'arguments' => [
            ['name' => 'min', 'type' => 'int', 'default' => '—', 'desc' => 'Минимальное значение'],
            ['name' => 'max', 'type' => 'int', 'default' => '—', 'desc' => 'Максимальное значение'],
            ['name' => 'step', 'type' => 'int/float', 'default' => '1', 'desc' => 'Шаг изменения'],
            ['name' => 'placeholder', 'type' => 'string', 'default' => '', 'desc' => 'Текст-подсказка'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Цена товара',
                'desc' => 'Числовое поле с ограничениями',
                'code' => "WP_Field::make([\n    'id' => 'product_price',\n    'type' => 'number',\n    'label' => 'Цена',\n    'min' => 0,\n    'step' => 0.01,\n    'placeholder' => '0.00'\n]);"
            ]
        ]
    ],
    
    // CHECKBOX
    'checkbox' => [
        'arguments' => [
            ['name' => 'label', 'type' => 'string', 'default' => '', 'desc' => 'Текст рядом с чекбоксом'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Простой чекбокс',
                'desc' => 'Включение/выключение опции',
                'code' => "WP_Field::make([\n    'id' => 'show_author',\n    'type' => 'checkbox',\n    'label' => 'Показывать автора'\n]);"
            ]
        ]
    ],
    
    // RADIO
    'radio' => [
        'arguments' => [
            ['name' => 'options', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив опций [key => label]'],
            ['name' => 'inline', 'type' => 'bool', 'default' => 'false', 'desc' => 'Горизонтальное расположение'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Выбор позиции',
                'desc' => 'Радиокнопки в линию',
                'code' => "WP_Field::make([\n    'id' => 'text_align',\n    'type' => 'radio',\n    'label' => 'Выравнивание',\n    'options' => [\n        'left' => 'Слева',\n        'center' => 'По центру',\n        'right' => 'Справа'\n    ],\n    'inline' => true\n]);"
            ]
        ]
    ],
    
    // ACCORDION
    'accordion' => [
        'arguments' => [
            ['name' => 'sections', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив секций с title, content, fields, open'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Группировка настроек',
                'desc' => 'Организация полей в аккордеон',
                'code' => "WP_Field::make([\n    'id' => 'settings_accordion',\n    'type' => 'accordion',\n    'label' => 'Настройки',\n    'sections' => [\n        [\n            'title' => 'Основные',\n            'open' => true,\n            'fields' => [\n                ['id' => 'title', 'type' => 'text', 'label' => 'Заголовок'],\n                ['id' => 'subtitle', 'type' => 'text', 'label' => 'Подзаголовок']\n            ]\n        ],\n        [\n            'title' => 'Дополнительные',\n            'fields' => [\n                ['id' => 'footer', 'type' => 'textarea', 'label' => 'Футер']\n            ]\n        ]\n    ]\n]);"
            ]
        ]
    ],
    
    // TABBED
    'tabbed' => [
        'arguments' => [
            ['name' => 'tabs', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив вкладок с title, icon, content, fields'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Вкладки с настройками',
                'desc' => 'Организация полей во вкладки',
                'code' => "WP_Field::make([\n    'id' => 'settings_tabs',\n    'type' => 'tabbed',\n    'tabs' => [\n        [\n            'title' => 'Общие',\n            'icon' => 'dashicons-admin-generic',\n            'fields' => [\n                ['id' => 'site_name', 'type' => 'text', 'label' => 'Название сайта']\n            ]\n        ],\n        [\n            'title' => 'Дизайн',\n            'icon' => 'dashicons-art',\n            'fields' => [\n                ['id' => 'primary_color', 'type' => 'color', 'label' => 'Основной цвет']\n            ]\n        ]\n    ]\n]);"
            ]
        ]
    ],
    
    // SPINNER
    'spinner' => [
        'arguments' => [
            ['name' => 'min', 'type' => 'int', 'default' => '0', 'desc' => 'Минимальное значение'],
            ['name' => 'max', 'type' => 'int', 'default' => '100', 'desc' => 'Максимальное значение'],
            ['name' => 'step', 'type' => 'int/float', 'default' => '1', 'desc' => 'Шаг изменения'],
            ['name' => 'unit', 'type' => 'string', 'default' => '', 'desc' => 'Единица измерения (px, %, em)'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Размер шрифта',
                'desc' => 'Спиннер с единицами измерения',
                'code' => "WP_Field::make([\n    'id' => 'font_size',\n    'type' => 'spinner',\n    'label' => 'Размер шрифта',\n    'min' => 10,\n    'max' => 72,\n    'step' => 1,\n    'unit' => 'px'\n]);"
            ]
        ]
    ],
    
    // BUTTON_SET
    'button_set' => [
        'arguments' => [
            ['name' => 'options', 'type' => 'array', 'default' => '[]', 'desc' => 'Массив опций [key => label]'],
            ['name' => 'multiple', 'type' => 'bool', 'default' => 'false', 'desc' => 'Множественный выбор'],
        ],
        'advanced_examples' => [
            [
                'title' => 'Выбор выравнивания',
                'desc' => 'Кнопки для выбора одного значения',
                'code' => "WP_Field::make([\n    'id' => 'text_align',\n    'type' => 'button_set',\n    'label' => 'Выравнивание текста',\n    'options' => [\n        'left' => 'Слева',\n        'center' => 'По центру',\n        'right' => 'Справа',\n        'justify' => 'По ширине'\n    ]\n]);"
            ]
        ]
    ],
];
