#!/bin/sh
docker-compose exec app sh composer install
docker-compose exec app sh php bin/console doctrine:migrations:migrate