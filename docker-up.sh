#!/usr/bin/env bash

SERVER_NAME="${1:-local.openstack.org}"

echo "SERVER NAME $SERVER_NAME";

MYSQL_SERVICE_PROVIDER=init USE_SWAP=0 SERVER_NAME=$SERVER_NAME vagrant up --provider=docker;