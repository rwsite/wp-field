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
            this.initSpinner();
            this.initSlider();
            this.initButtonSet();
            this.initAccordion();
            this.initTabbed();
            this.initImageSelect();
            this.initCodeEditor();
            this.initIconPicker();
            this.initMap();
            this.initSortable();
            this.initSorter();
            this.initPalette();
            this.initBackup();
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
            const self = this;
            
            // Проверяем доступность wp.colorPicker
            if (typeof $ === 'undefined' || typeof $.fn.wpColorPicker === 'undefined') {
                // Если не загружен, пробуем через 100мс
                setTimeout(function() {
                    self.initColorPicker();
                }, 100);
                return;
            }
            
            // Инициализируем только те поля, которые еще не инициализированы
            $('.wp-color-picker-field').each(function() {
                const $field = $(this);
                if (!$field.hasClass('wp-color-picker')) {
                    try {
                        // Проверяем, нужна ли поддержка альфа-канала
                        const enableAlpha = $field.data('alpha') !== false;
                        
                        $field.wpColorPicker({
                            // Настройки по умолчанию
                            defaultColor: $field.data('default-color') || false,
                            palettes: true
                        });
                        
                        // Если включен альфа-канал, добавляем слайдер прозрачности
                        if (enableAlpha) {
                            self.addAlphaSlider($field);
                        }
                    } catch (e) {
                        console.error('Color Picker init error:', e);
                    }
                }
            });
        },

        /**
         * Добавить слайдер альфа-канала к Color Picker
         */
        addAlphaSlider: function($field) {
            const self = this;
            
            // Небольшая задержка для полной инициализации wpColorPicker
            setTimeout(function() {
                const $wrapper = $field.closest('.wp-picker-container');
                const $irisContainer = $wrapper.find('.iris-picker');
                
                if ($irisContainer.length === 0) {
                    return;
                }
                
                // Проверяем, не добавлен ли уже слайдер
                if ($irisContainer.find('.iris-alpha-slider').length > 0) {
                    return;
                }
                
                // Создаем слайдер альфа-канала
                const $alphaSlider = $('<div class="iris-alpha-slider"></div>');
                const $alphaSliderInner = $('<div class="iris-alpha-slider-inner"></div>');
                const $alphaValue = $('<div class="iris-alpha-value">100%</div>');
                
                $alphaSlider.append($alphaSliderInner);
                $alphaSlider.append($alphaValue);
                $irisContainer.append($alphaSlider);
                
                // Получаем текущий цвет и альфа
                const color = $field.wpColorPicker('color');
                let alpha = 1;
                
                if (color && color.indexOf('rgba') !== -1) {
                    const rgba = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+),?\s*(\d*\.?\d+)?\)/);
                    alpha = rgba && rgba[4] ? parseFloat(rgba[4]) : 1;
                }
                
                // Устанавливаем начальное значение процента
                $alphaValue.text(Math.round(alpha * 100) + '%');
                
                // Инициализируем jQuery UI slider
                $alphaSliderInner.slider({
                    orientation: 'vertical',
                    min: 0,
                    max: 100,
                    value: alpha * 100,
                    slide: function(event, ui) {
                        const newAlpha = ui.value / 100;
                        const currentColor = $field.wpColorPicker('color');
                        const newColor = self.updateColorAlpha(currentColor, newAlpha);
                        
                        // Обновляем значение поля
                        $field.val(newColor);
                        
                        // Обновляем отображение процента
                        $alphaValue.text(Math.round(newAlpha * 100) + '%');
                        
                        // Обновляем фон слайдера
                        self.updateAlphaSliderBackground($alphaSliderInner, currentColor);
                    },
                    create: function() {
                        const currentColor = $field.wpColorPicker('color');
                        if (currentColor) {
                            self.updateAlphaSliderBackground($alphaSliderInner, currentColor);
                        }
                    }
                });
                
                // Обновляем слайдер при изменении цвета через палитру
                $field.wpColorPicker('instance').picker.on('irischange', function(event, ui) {
                    if (ui && ui.color) {
                        const currentColor = ui.color.toString();
                        self.updateAlphaSliderBackground($alphaSliderInner, currentColor);
                        
                        // Обновляем позицию слайдера если цвет содержит альфа
                        if (currentColor.indexOf('rgba') !== -1) {
                            const rgba = currentColor.match(/rgba?\((\d+),\s*(\d+),\s*(\d+),?\s*(\d*\.?\d+)?\)/);
                            if (rgba && rgba[4]) {
                                const newAlpha = parseFloat(rgba[4]);
                                $alphaSliderInner.slider('value', newAlpha * 100);
                            }
                        }
                    }
                });
            }, 100);
        },

        /**
         * Обновить цвет с новым значением альфа
         */
        updateColorAlpha: function(color, alpha) {
            if (color.indexOf('rgb') !== -1) {
                const rgb = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
                if (rgb) {
                    return 'rgba(' + rgb[1] + ', ' + rgb[2] + ', ' + rgb[3] + ', ' + alpha + ')';
                }
            } else if (color.indexOf('#') !== -1) {
                const hex = color.replace('#', '');
                const r = parseInt(hex.substring(0, 2), 16);
                const g = parseInt(hex.substring(2, 4), 16);
                const b = parseInt(hex.substring(4, 6), 16);
                return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
            }
            return color;
        },

        /**
         * Обновить фон слайдера альфа-канала
         */
        updateAlphaSliderBackground: function($slider, color) {
            let rgb = '255, 255, 255';
            
            if (color.indexOf('rgb') !== -1) {
                const match = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
                if (match) {
                    rgb = match[1] + ', ' + match[2] + ', ' + match[3];
                }
            } else if (color.indexOf('#') !== -1) {
                const hex = color.replace('#', '');
                const r = parseInt(hex.substring(0, 2), 16);
                const g = parseInt(hex.substring(2, 4), 16);
                const b = parseInt(hex.substring(4, 6), 16);
                rgb = r + ', ' + g + ', ' + b;
            }
            
            $slider.css({
                background: 'linear-gradient(to top, rgba(' + rgb + ', 0) 0%, rgba(' + rgb + ', 1) 100%)'
            });
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
                const $wrapper = $(this).closest('.wp-field-image-wrapper');
                $wrapper.find('.wp-field-image-id').val('');
                $wrapper.find('.wp-field-image-url').val('');
                $wrapper.find('.wp-field-image-preview-wrapper').remove();
                $(this).remove();
            });

            // File button
            $(document).on('click', '.wp-field-file-button', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                const library = $(this).data('library') || '';
                self.openMediaFrame(fieldId, 'file', library);
            });

            // Gallery add button
            $(document).on('click', '.wp-field-gallery-add', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.openMediaFrame(fieldId, 'gallery');
            });
            
            // Gallery edit button
            $(document).on('click', '.wp-field-gallery-edit', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                self.openMediaFrame(fieldId, 'gallery', '', true);
            });
            
            // Gallery clear button
            $(document).on('click', '.wp-field-gallery-clear', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                const $wrapper = $(this).closest('.wp-field-gallery-wrapper');
                $wrapper.find('.wp-field-gallery-ids').val('');
                $wrapper.find('.wp-field-gallery-preview').empty();
            });
            
            // Gallery remove single item
            $(document).on('click', '.wp-field-gallery-remove', function (e) {
                e.preventDefault();
                const imageId = $(this).data('id');
                const $item = $(this).closest('.wp-field-gallery-item');
                const $wrapper = $item.closest('.wp-field-gallery-wrapper');
                const $input = $wrapper.find('.wp-field-gallery-ids');
                
                // Удаляем из списка ID
                let ids = $input.val().split(',').filter(id => id != imageId);
                $input.val(ids.join(','));
                
                // Удаляем элемент
                $item.remove();
            });

            // Media button
            $(document).on('click', '.wp-field-media-button', function (e) {
                e.preventDefault();
                const fieldId = $(this).data('field-id');
                const library = $(this).data('library') || '';
                self.openMediaFrame(fieldId, 'media', library);
            });

            // Background image button
            $(document).on('click', '.wp-field-background-image-button', function (e) {
                e.preventDefault();
                const fieldName = $(this).data('field-name');
                self.openBackgroundImageFrame(fieldName);
            });
        },

        /**
         * Открыть media frame
         */
        openMediaFrame: function (fieldId, type, library, isEdit) {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                console.error('wp.media не найден');
                return;
            }

            const self = this;
            const $wrapper = $('.wp-field-' + type + '-wrapper').has('[data-field-id="' + fieldId + '"]');
            const $input = $wrapper.find('input[type="hidden"]');

            // Настройка library в зависимости от типа
            let libraryConfig = {};
            if (library) {
                libraryConfig = { type: library };
            } else if (type === 'image' || type === 'gallery') {
                libraryConfig = { type: 'image' };
            }

            let frame = wp.media({
                title: this.getMediaTitle(type),
                button: {
                    text: this.getMediaButtonText(type)
                },
                multiple: type === 'gallery',
                library: libraryConfig
            });
            
            // Для gallery в режиме редактирования предвыбираем текущие изображения
            if (type === 'gallery' && isEdit) {
                frame.on('open', function() {
                    const selection = frame.state().get('selection');
                    const ids = $input.val().split(',').filter(id => id);
                    ids.forEach(function(id) {
                        const attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment);
                    });
                });
            }

            frame.on('select', function () {
                const selection = frame.state().get('selection');

                if (type === 'gallery') {
                    const ids = selection.map(function (attachment) {
                        return attachment.id;
                    });
                    $input.val(ids.join(','));
                    
                    // Обновляем превью
                    self.updateGalleryPreview($wrapper, selection);
                } else {
                    const attachment = selection.first().toJSON();
                    $input.val(attachment.id);

                    // Обновляем URL поле
                    const $urlInput = $wrapper.find('.wp-field-' + type + '-url');
                    if ($urlInput.length) {
                        $urlInput.val(attachment.url);
                    }

                    // Обновляем preview для image
                    if (type === 'image') {
                        let $previewWrapper = $wrapper.find('.wp-field-image-preview-wrapper');
                        if (!$previewWrapper.length) {
                            $previewWrapper = $('<div class="wp-field-image-preview-wrapper"></div>');
                            $wrapper.append($previewWrapper);
                        }
                        $previewWrapper.html('<img src="' + attachment.url + '" alt="" class="wp-field-image-preview">');
                        
                        // Добавляем кнопку удаления если её нет
                        if (!$wrapper.find('.wp-field-image-remove').length) {
                            const $removeBtn = $('<button type="button" class="button wp-field-image-remove" data-field-id="' + fieldId + '">Удалить</button>');
                            $wrapper.find('.wp-field-image-button').after($removeBtn);
                        }
                    }
                    
                    // Обновляем имя файла для file
                    if (type === 'file') {
                        let $fileName = $wrapper.find('.wp-field-file-name');
                        if (!$fileName.length) {
                            $fileName = $('<span class="wp-field-file-name"></span>');
                            $wrapper.append($fileName);
                        }
                        $fileName.text(attachment.filename);
                    }
                    
                    // Обновляем preview для media
                    if (type === 'media') {
                        let $preview = $wrapper.find('.wp-field-media-preview');
                        if (!$preview.length) {
                            $preview = $('<div class="wp-field-media-preview"></div>');
                            $wrapper.append($preview);
                        }
                        
                        if (attachment.type === 'image') {
                            $preview.html('<img src="' + attachment.url + '" alt="">');
                        } else {
                            $preview.html('<span class="wp-field-media-filename">' + attachment.filename + '</span>');
                        }
                    }
                }
            });

            frame.open();
        },
        
        /**
         * Обновить превью галереи
         */
        updateGalleryPreview: function($wrapper, selection) {
            const $preview = $wrapper.find('.wp-field-gallery-preview');
            $preview.empty();
            
            selection.each(function(attachment) {
                const data = attachment.toJSON();
                const thumbUrl = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
                const $item = $('<div class="wp-field-gallery-item" data-id="' + data.id + '">' +
                    '<img src="' + thumbUrl + '" alt="">' +
                    '<span class="wp-field-gallery-remove" data-id="' + data.id + '">×</span>' +
                    '</div>');
                $preview.append($item);
            });
        },

        /**
         * Открыть media frame для background image
         */
        openBackgroundImageFrame: function (fieldName) {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                console.error('wp.media не найден');
                return;
            }

            const $input = $('input[name="' + fieldName + '[image]"]');

            let frame = wp.media({
                title: 'Выберите фоновое изображение',
                button: {
                    text: 'Выбрать изображение'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            frame.on('select', function () {
                const attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.id);
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
        },

        /**
         * Инициализация spinner (счётчик с кнопками)
         */
        initSpinner: function () {
            $(document).on('click', '.wp-field-spinner-up', function (e) {
                e.preventDefault();
                const $btn = $(this);
                const $input = $btn.siblings('.wp-field-spinner-input-wrap').find('input');
                const step = parseFloat($btn.data('step')) || 1;
                const max = parseFloat($input.attr('max'));
                const current = parseFloat($input.val()) || 0;
                const newValue = current + step;

                if (!max || newValue <= max) {
                    $input.val(newValue).trigger('change');
                }
            });

            $(document).on('click', '.wp-field-spinner-down', function (e) {
                e.preventDefault();
                const $btn = $(this);
                const $input = $btn.siblings('.wp-field-spinner-input-wrap').find('input');
                const step = parseFloat($btn.data('step')) || 1;
                const min = parseFloat($input.attr('min'));
                const current = parseFloat($input.val()) || 0;
                const newValue = current - step;

                if (typeof min === 'undefined' || newValue >= min) {
                    $input.val(newValue).trigger('change');
                }
            });
        },

        /**
         * Инициализация slider (ползунок)
         */
        initSlider: function () {
            $(document).on('input', '.wp-field-slider', function () {
                const $slider = $(this);
                const $value = $slider.siblings('.wp-field-slider-value');

                if ($value.length) {
                    $value.text($slider.val());
                }
            });
        },

        /**
         * Инициализация button_set (группа кнопок)
         */
        initButtonSet: function () {
            $(document).on('change', '.wp-field-button-set input', function () {
                const $input = $(this);
                const $label = $input.closest('.wp-field-button-set-item');

                if ($input.is(':checked')) {
                    $label.addClass('active');
                    // Для radio кнопок убираем active с других
                    if ($input.attr('type') === 'radio') {
                        $input.closest('.wp-field-button-set').find('.wp-field-button-set-item').not($label).removeClass('active');
                    }
                } else {
                    $label.removeClass('active');
                }
            });

            // Инициализация активных кнопок при загрузке
            $('.wp-field-button-set input:checked').closest('.wp-field-button-set-item').addClass('active');
        },

        /**
         * Инициализация accordion (свёртываемые секции)
         */
        initAccordion: function () {
            $(document).on('click', '.wp-field-accordion-header', function () {
                const $item = $(this).closest('.wp-field-accordion-item');
                const $content = $item.find('.wp-field-accordion-content');
                const $icon = $item.find('.wp-field-accordion-icon');

                if ($item.hasClass('is-open')) {
                    $item.removeClass('is-open');
                    $icon.text('▶');
                } else {
                    $item.addClass('is-open');
                    $icon.text('▼');
                }
            });
        },

        /**
         * Инициализация tabbed (вкладки)
         */
        initTabbed: function () {
            $(document).on('click', '.wp-field-tabbed-nav-item', function () {
                const $button = $(this);
                const tabId = $button.data('tab');
                const $tabbed = $button.closest('.wp-field-tabbed');

                // Убираем active со всех кнопок и панелей
                $tabbed.find('.wp-field-tabbed-nav-item').removeClass('active');
                $tabbed.find('.wp-field-tabbed-pane').removeClass('active');

                // Добавляем active к текущей кнопке и панели
                $button.addClass('active');
                $tabbed.find('[data-tab="' + tabId + '"]').filter('.wp-field-tabbed-pane').addClass('active');
            });
        },

        /**
         * Инициализация image_select (выбор из изображений)
         */
        initImageSelect: function () {
            $(document).on('change', '.wp-field-image-select input[type="radio"]', function () {
                const $input = $(this);
                const $container = $input.closest('.wp-field-image-select');

                // Убираем selected со всех элементов
                $container.find('.wp-field-image-select-item').removeClass('selected');

                // Добавляем selected к выбранному
                $input.closest('.wp-field-image-select-item').addClass('selected');
            });

            // Инициализация при загрузке
            $('.wp-field-image-select input[type="radio"]:checked').closest('.wp-field-image-select-item').addClass('selected');
        },

        /**
         * Инициализация code_editor (редактор кода)
         */
        initCodeEditor: function () {
            if (typeof wp === 'undefined' || typeof wp.codeEditor === 'undefined') {
                return;
            }

            $('.wp-field-code-editor').each(function () {
                const $textarea = $(this);
                const mode = $textarea.data('mode') || 'css';

                wp.codeEditor.initialize($textarea[0], {
                    type: mode,
                    codemirror: {
                        lineNumbers: true,
                        mode: mode,
                        theme: 'default'
                    }
                });
            });
        },

        /**
         * Инициализация icon picker (выбор иконки)
         */
        initIconPicker: function () {
            const self = this;

            // Открыть modal
            $(document).on('click', '.wp-field-icon-button', function (e) {
                e.preventDefault();
                const $picker = $(this).closest('.wp-field-icon-picker');
                const $modal = $picker.find('.wp-field-icon-modal');
                $modal.show();
            });

            // Закрыть modal
            $(document).on('click', '.wp-field-icon-close', function (e) {
                e.preventDefault();
                $(this).closest('.wp-field-icon-modal').hide();
            });

            // Выбрать иконку
            $(document).on('click', '.wp-field-icon-grid span', function () {
                const $icon = $(this);
                const iconClass = $icon.data('icon');
                const library = $icon.attr('class').split(' ')[0];
                const $picker = $icon.closest('.wp-field-icon-picker');
                const $input = $picker.find('.wp-field-icon-value');
                const $button = $picker.find('.wp-field-icon-button');

                $input.val(iconClass);
                $button.html('<span class="' + library + ' ' + iconClass + '"></span> ' + iconClass);
                $picker.find('.wp-field-icon-modal').hide();
            });

            // Поиск иконок
            $(document).on('input', '.wp-field-icon-search', function () {
                const query = $(this).val().toLowerCase();
                const $grid = $(this).closest('.wp-field-icon-modal').find('.wp-field-icon-grid span');

                $grid.each(function () {
                    const iconName = $(this).data('icon').toLowerCase();
                    if (iconName.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        },

        /**
         * Инициализация map (Google Maps)
         */
        initMap: function () {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                return;
            }

            $('.wp-field-map').each(function () {
                const $mapEl = $(this);
                const $wrapper = $mapEl.closest('.wp-field-map-wrapper');
                const $lat = $wrapper.find('.wp-field-map-lat');
                const $lng = $wrapper.find('.wp-field-map-lng');

                const zoom = parseInt($mapEl.data('zoom')) || 12;
                const centerLat = parseFloat($lat.val()) || parseFloat($mapEl.data('center-lat')) || 55.7558;
                const centerLng = parseFloat($lng.val()) || parseFloat($mapEl.data('center-lng')) || 37.6173;

                const map = new google.maps.Map($mapEl[0], {
                    center: { lat: centerLat, lng: centerLng },
                    zoom: zoom
                });

                const marker = new google.maps.Marker({
                    position: { lat: centerLat, lng: centerLng },
                    map: map,
                    draggable: true
                });

                google.maps.event.addListener(marker, 'dragend', function () {
                    const pos = marker.getPosition();
                    $lat.val(pos.lat());
                    $lng.val(pos.lng());
                });

                // Клик по карте для установки маркера
                google.maps.event.addListener(map, 'click', function (event) {
                    marker.setPosition(event.latLng);
                    $lat.val(event.latLng.lat());
                    $lng.val(event.latLng.lng());
                });
            });
        },

        /**
         * Инициализация sortable (сортируемый список)
         */
        initSortable: function () {
            if (typeof $.fn.sortable === 'undefined') {
                return;
            }

            $('.wp-field-sortable').sortable({
                handle: '.dashicons-menu',
                placeholder: 'wp-field-sortable-placeholder',
                cursor: 'move',
                opacity: 0.7
            });
        },

        /**
         * Инициализация sorter (сортировщик с enabled/disabled)
         */
        initSorter: function () {
            if (typeof $.fn.sortable === 'undefined') {
                return;
            }

            $('.wp-field-sorter-list').sortable({
                handle: '.dashicons-menu',
                placeholder: 'wp-field-sorter-placeholder',
                cursor: 'move',
                opacity: 0.7,
                connectWith: '.wp-field-sorter-list',
                receive: function (event, ui) {
                    const $item = ui.item;
                    const $list = $(this);
                    const type = $list.data('type');
                    const $input = $item.find('input');
                    const name = $input.attr('name');

                    // Обновляем name в зависимости от колонки
                    if (name) {
                        const baseName = name.replace(/\[(enabled|disabled)\]/, '');
                        $input.attr('name', baseName + '[' + type + '][]');
                    }
                }
            });
        },

        /**
         * Инициализация palette (палитра цветов)
         */
        initPalette: function () {
            $(document).on('change', '.wp-field-palette input[type="radio"]', function () {
                const $input = $(this);
                const $container = $input.closest('.wp-field-palette');

                // Убираем selected со всех элементов
                $container.find('.wp-field-palette-item').removeClass('selected');

                // Добавляем selected к выбранному
                $input.closest('.wp-field-palette-item').addClass('selected');
            });

            // Инициализация при загрузке
            $('.wp-field-palette input[type="radio"]:checked').closest('.wp-field-palette-item').addClass('selected');
        },

        /**
         * Инициализация backup (экспорт/импорт)
         */
        initBackup: function () {
            // Copy to clipboard
            $(document).on('click', '.wp-field-backup-copy', function (e) {
                e.preventDefault();
                const $textarea = $(this).siblings('.wp-field-backup-export');
                $textarea[0].select();
                document.execCommand('copy');
                alert('Скопировано в буфер обмена!');
            });

            // Download JSON
            $(document).on('click', '.wp-field-backup-download', function (e) {
                e.preventDefault();
                const $textarea = $(this).siblings('.wp-field-backup-export');
                const data = $textarea.val();
                const blob = new Blob([data], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'wp-field-backup-' + Date.now() + '.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });

            // Validate JSON
            $(document).on('click', '.wp-field-backup-validate', function (e) {
                e.preventDefault();
                const $textarea = $(this).siblings('.wp-field-backup-import');
                const $status = $(this).siblings('.wp-field-backup-status');
                const data = $textarea.val();

                if (!data.trim()) {
                    $status.html('<p class="error">Пожалуйста, вставьте JSON данные</p>');
                    return;
                }

                try {
                    JSON.parse(data);
                    $status.html('<p class="success">✓ JSON валиден и готов к импорту</p>');
                } catch (e) {
                    $status.html('<p class="error">✗ Ошибка JSON: ' + e.message + '</p>');
                }
            });
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
