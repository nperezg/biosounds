#!/bin/sh

# Install BioSounds

docker-compose up -d
docker-compose exec apache composer install
docker-compose up -d database
docker exec -i "$(docker-compose ps -q database)" mysql -ubiosounds -pbiosounds biosounds < init.sql
docker exec -i "$(docker-compose ps -q database)" mysql -ubiosounds -pbiosounds biosounds < data.sql
