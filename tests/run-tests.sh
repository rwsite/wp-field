#!/bin/bash

# Скрипт для запуска всех тестов WP_Field v2.4.11

echo "=== WP_Field v2.4.11 Test Runner ==="
echo ""

# Проверяем, есть ли Pest
if command -v ./vendor/bin/pest &> /dev/null; then
    echo "✓ Pest найден"
    echo ""
    echo "Запуск тестов через Pest..."
    ./vendor/bin/pest
else
    echo "✗ Pest не найден"
    echo ""
    echo "Способы запуска тестов:"
    echo ""
    echo "1. Через PHP (простой способ):"
    echo "   php tests/test-wp-field-v2.4.php"
    echo ""
    echo "2. Через Pest (требует composer install):"
    echo "   composer install"
    echo "   ./vendor/bin/pest"
    echo ""
    echo "3. Через PHPUnit:"
    echo "   ./vendor/bin/phpunit tests/"
fi
