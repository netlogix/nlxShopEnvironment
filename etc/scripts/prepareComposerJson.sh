#!/usr/bin/env bash

echo "Preparing composer.json..."
echo "PHP version:      "$(php -r "echo PHP_VERSION;")
echo "Shopware version: "$SHOPWARE_VERSION
echo "Composer version: "$(composer -V)

sed -i "s~[\"]shopware/shopware[\"]: [\"]^5.2[\"]~\"shopware/shopware\": \"${SHOPWARE_VERSION}\"~g" composer.json
