#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")

./installComposer.sh

echo "[Symfony tests] Check configuration"
docker run -it --rm --name symfony1_tests \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
    php:8 php data/bin/check_configuration.php

echo "[Symfony tests] Run test suites"
docker run -it --rm --name symfony1_tests \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
    php:8 data/bin/symfony symfony:test --trace
