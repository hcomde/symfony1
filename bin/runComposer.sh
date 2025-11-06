#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")
source "${SCRIPT_DIR}/projectVars.sh"

# Running composer install
docker run -it --rm --tty --name symfony1_composer \
  -v "${SCRIPT_DIR}/../:/app" \
  composer:${COMPOSER_TAG} "${@}"
