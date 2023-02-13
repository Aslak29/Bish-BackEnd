#!/usr/bin/env bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:fixture:load --no-interaction
exec "$@"