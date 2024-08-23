#!/bin/bash
set -e
export DOCKER_SCAN_SUGGEST=false

docker compose down
docker compose build --no-cache
docker compose up -d --build --force-recreate