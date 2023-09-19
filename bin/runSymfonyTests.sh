#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")
source "${SCRIPT_DIR}/projectVars.sh"
source "${SCRIPT_DIR}/runComposer.sh" install --optimize-autoloader

echo "[Symfony tests] Check configuration"
docker run -it --rm --name symfony1_tests \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
  "${PHP_IMAGE_TAG}" php data/bin/check_configuration.php

echo "[Symfony tests] Run test suites"
docker run -it --rm --name symfony1_tests \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
  -u "$(id -u):$(id -g)" \
  "${PHP_IMAGE_TAG}" data/bin/symfony symfony:test
