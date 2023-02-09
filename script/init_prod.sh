#!/bin/sh
docker-compose exec app composer install
docker-compose exec app php bin/console doctrine:migrations:migrate