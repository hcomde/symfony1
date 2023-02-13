#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")

./installComposer.sh

echo "[PHPUnit] Run test suites"
docker run -it --rm --name symfony1 \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
    php:8 vendor/bin/phpunit /usr/src/symfony1/test/phpunit
