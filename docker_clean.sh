#!/bin/bash
set -e

docker rm -v -f $(docker ps -qa)
docker image remove -f $(sudo docker images -a -q)