#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")
source "${SCRIPT_DIR}/projectVars.sh"

# Build docker container for latest PHP
docker build \
  --tag "${PHP_IMAGE_TAG}" \
  --build-arg="PHP_VERSION=${PHP_VERSION}" \
  --build-arg="APCU_VERSION=${APCU_VERSION}" \
  --build-arg="MEMCACHE_VERSION=${MEMCACHE_VERSION}" \
  --build-arg="XDEBUG_VERSION=${XDEBUG_VERSION}" \
  "${SCRIPT_DIR}/../.docker/${DOCKERFILE}"
