#!/bin/bash
set -e
export DOCKER_SCAN_SUGGEST=false
docker compose run --rm app composer install
docker compose run --rm app ./framework/sake installsake;
docker compose run --rm app yarn install
docker compose run --rm app ./sass.sh
docker compose run --rm app yarn build-all
docker compose up -d
docker compose exec app /bin/bash ./framework/sake installsake;
docker compose exec app /bin/bash sake dev/build flush=1
docker compose exec app /bin/bash sake dev/tasks/DBMigrateTask
docker compose exec app /bin/bash