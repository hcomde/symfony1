#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")
source "${SCRIPT_DIR}/projectVars.sh"
source "${SCRIPT_DIR}/runComposer.sh" install --optimize-autoloader

echo "[PHPUnit] Run test suites"
docker run -it --rm --name symfony1_phpunit \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
  "${PHP_IMAGE_TAG}" vendor/bin/phpunit /usr/src/symfony1/test/phpunit
