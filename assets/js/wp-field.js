/**
 * WP_Field - Universal field renderer for WordPress
 * Инициализация интерактивных элементов: зависимости, media, color picker, repeater
 */

(function ($) {
    'use strict';

    const WPField = {
        /**
         * Инициализация при загрузке документа
         */
        init: function () {
            this.initDependencies();
            this.initColorPicker();
            this.initMediaButtons();
            this.initRepeater();
        },

        /**
         * Инициализация системы зависимостей
         */
        initDependencies: function () {
            const self = this;

            // Слушаем изменения всех полей
            $(document).on('change input', '[data-field-id]', function () {
                const fieldId = $(this).data('field-id');
                self.updateDependentFields(fieldId);
            });

            // Первичная инициализация
            $('[data-dependency]').each(function () {
                const $field = $(this);
                const dependency = $field.data('dependency');

                if (dependency && self.evaluateDependency(dependency)) {
                    $field.removeClass('is-hidden');
                } else {
                    $field.addClass('is-hidden');
                }
            });
        },

        /**
         * Обновить видимость зависимых полей
         */
        updateDependentFields: function (fieldId) {
            const self = this;

            $('[data-dependency]').each(function () {
                const $field = $(this);
                const dependency = $field.data('dependency');

                if (!dependency) return;

                // Проверяем, зависит ли это поле от изменённого поля
                let isDependentOnChanged = false;

                if (Array.isArray(dependency)) {
                    dependency.forEach(function (condition) {
                        if (Array.isArray(condition) && condition[0] === fieldId) {
                            isDependentOnChanged = true;
                        }
                    });
                }

                if (isDependentOnChanged) {
                    if (self.evaluateDependency(dependency)) {
                        $field.removeClass('is-hidden').attr('aria-hidden', 'false');
                    } else {
                        $field.addClass('is-hidden').attr('aria-hidden', 'true');
                    }
                }
            });
        },

        /**
         * Оценить условие зависимости на фронте
         */
        evaluateDependency: function (dependency) {
            if (!dependency || typeof dependency !== 'object') {
                return true;
            }

            const relation = dependency.relation || 'AND';
            const conditions = [];

            // Собираем условия (пропускаем 'relation')
            for (const key in dependency) {
                if (key !== 'relation' && Array.isArray(dependency[key])) {
                    conditions.push(dependency[key]);
                }
            }

            if (conditions.length === 0) {
                return true;
            }

            const results = conditions.map(condition => {
                return this.evaluateCondition(condition);
            });

            if (relation === 'AND') {
                return results.every(r => r === true);
            } else {
                return results.some(r => r === true);
            }
        },

        /**
         * Оценить одно условие
         */
        evaluateCondition: function (condition) {
            if (!Array.isArray(condition) || condition.length < 3) {
                return true;
            }

            const [fieldId, operator, compareValue] = condition;
            const fieldValue = this.getFieldValue(fieldId);

            switch (operator) {
                case '==':
                    return fieldValue == compareValue;
                case '!=':
                    return fieldValue != compareValue;
                case '>':
                    return fieldValue > compareValue;
                case '>=':
                    return fieldValue >= compareValue;
                case '<':
                    return fieldValue < compareValue;
                case '<=':
                    return fieldValue <= compareValue;
                case 'in':
                    return Array.isArray(compareValue) && compareValue.includes(fieldValue);
                case 'not_in':
                    return Array.isArray(compareValue) && !compareValue.includes(fieldValue);
                case 'contains':
                    return String(fieldValue).includes(String(compareValue));
                case 'not_contains':
                    return !String(fieldValue).includes(String(compareValue));
                case 'empty':
                    return !fieldValue || fieldValue === '';
                case 'not_empty':
                    return fieldValue && fieldValue !== '';
                default:
                    return false;
            }
        },

        /**
         * Получить значение поля по ID
         */
        getFieldValue: function (fieldId) {
            const $input = $('[data-field-id="' + fieldId + '"] input, [data-field-id="' + fieldId + '"] select, [data-field-id="' + fieldId + '"] textarea');

            if ($input.length === 0) {
                return null;
            }

            if ($input.is(':checkbox')) {
                return $input.is(':checked') ? '1' : '';
            }

            if ($input.is(':radio')) {
                return $('input[name="' + $input.attr('name') + '"]:checked').val() || '';
            }

            if ($input.is('select')) {
                return $input.val();
            }

            return $input.val() || '';
        },

        /**
         * Инициализация color picker
         */
        initColorPicker: function () {
            if (typeof wp !== 'undefined' && typeof wp.colorPicker !== 'undefined') {
                $('.wp-color-picker-field').wpColorPicker();
            }
        },

        /**
         * Инициализация media buttons
         */
        initMediaButtons: function () {
            const self = this;

            // Image button
            $(document).on('click', '.wp-field-image-button', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.openMediaFrame(fieldId, 'image');
            });

            // Image remove button
            $(document).on('click', '.wp-field-image-remove', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                const $field = $('[data-field-id="' + fieldId + '"]');
                $field.find('.wp-field-image-id').val('');
                $field.find('.wp-field-image-preview').attr('src', 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIj48cmVjdCBmaWxsPSIjZTBlMGUwIiB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIvPjwvc3ZnPg==');
            });

            // File button
            $(document).on('click', '.wp-field-file-button', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.openMediaFrame(fieldId, 'file');
            });

            // Gallery button
            $(document).on('click', '.wp-field-gallery-button', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.openMediaFrame(fieldId, 'gallery');
            });

            // Media button
            $(document).on('click', '.wp-field-media-button', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.openMediaFrame(fieldId, 'media');
            });
        },

        /**
         * Открыть media frame
         */
        openMediaFrame: function (fieldId, type) {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                console.error('wp.media не найден');
                return;
            }

            const $field = $('[data-field-id="' + fieldId + '"]');
            const $input = $field.find('input[type="hidden"]');

            let frame = wp.media({
                title: this.getMediaTitle(type),
                button: {
                    text: this.getMediaButtonText(type)
                },
                multiple: type === 'gallery',
                library: {
                    type: type === 'file' ? '' : type
                }
            });

            frame.on('select', function () {
                const selection = frame.state().get('selection');

                if (type === 'gallery') {
                    const ids = selection.map(function (attachment) {
                        return attachment.id;
                    });
                    $input.val(ids.join(','));
                } else {
                    const attachment = selection.first().toJSON();
                    $input.val(attachment.id);

                    // Обновляем preview для image
                    if (type === 'image') {
                        $field.find('.wp-field-image-preview').attr('src', attachment.url);
                    }
                }
            });

            frame.open();
        },

        /**
         * Получить заголовок для media frame
         */
        getMediaTitle: function (type) {
            const titles = {
                'image': 'Выберите изображение',
                'file': 'Выберите файл',
                'gallery': 'Выберите галерею',
                'media': 'Выберите медиа'
            };
            return titles[type] || 'Выберите медиа';
        },

        /**
         * Получить текст кнопки для media frame
         */
        getMediaButtonText: function (type) {
            const texts = {
                'image': 'Выбрать изображение',
                'file': 'Выбрать файл',
                'gallery': 'Выбрать галерею',
                'media': 'Выбрать медиа'
            };
            return texts[type] || 'Выбрать';
        },

        /**
         * Инициализация repeater
         */
        initRepeater: function () {
            const self = this;

            // Add button
            $(document).on('click', '.wp-field-repeater-add', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.addRepeaterItem(fieldId);
            });

            // Remove button
            $(document).on('click', '.wp-field-repeater-remove', function (e) {
                e.preventDefault();
                $(this).closest('.wp-field-repeater-item').remove();
            });
        },

        /**
         * Добавить элемент в repeater
         */
        addRepeaterItem: function (fieldId) {
            const $repeater = $('[data-field-id="' + fieldId + '"].wp-field-repeater');
            const $template = $repeater.find('.wp-field-repeater-template');

            if ($template.length === 0) {
                console.warn('Template не найден для repeater: ' + fieldId);
                return;
            }

            // Получаем максимальный индекс
            const maxIndex = Math.max(
                0,
                ...Array.from($repeater.find('.wp-field-repeater-item:not(.wp-field-repeater-template)'))
                    .map(el => parseInt($(el).data('index')) || 0)
            );

            const newIndex = maxIndex + 1;
            const $newItem = $template.clone().removeClass('wp-field-repeater-template').attr('data-index', newIndex);

            // Обновляем ID и name в клонированных полях
            $newItem.find('[data-field-id]').each(function () {
                const $el = $(this);
                const fieldId = $el.data('field-id');
                $el.attr('data-field-id', fieldId + '_' + newIndex);

                $el.find('input, select, textarea').each(function () {
                    const $input = $(this);
                    const name = $input.attr('name');
                    if (name) {
                        $input.attr('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
                    }
                });
            });

            $repeater.append($newItem);

            // Проверяем лимит
            this.checkRepeaterLimit(fieldId);
        },

        /**
         * Проверить лимит repeater
         */
        checkRepeaterLimit: function (fieldId) {
            const $repeater = $('[data-field-id="' + fieldId + '"].wp-field-repeater');
            const max = parseInt($repeater.data('max')) || 0;
            const count = $repeater.find('.wp-field-repeater-item:not(.wp-field-repeater-template)').length;
            const $addButton = $('[data-field-id="' + fieldId + '"].wp-field-repeater-add');

            if (max > 0 && count >= max) {
                $addButton.prop('disabled', true);
            } else {
                $addButton.prop('disabled', false);
            }
        }
    };

    // Инициализация при готовности документа
    $(document).ready(function () {
        WPField.init();
    });

    // Для динамически добавленного контента
    $(document).on('wp-field-ready', function () {
        WPField.init();
    });

    // Экспортируем для использования извне
    window.WPField = WPField;

})(jQuery);
