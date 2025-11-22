#!/bin/bash

echo "🧪 Running WP_Field tests from project root..."
echo "Note: Coverage requires Xdebug/PCOV. Running tests without coverage..."
echo ""

cd ../../../../plugins/woo2iiko && ./vendor/bin/phpunit lib/wp-field/tests/Unit/FieldInitializationTest.php
