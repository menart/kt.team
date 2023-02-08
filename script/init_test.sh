#!/bin/sh
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate -n --env=test

