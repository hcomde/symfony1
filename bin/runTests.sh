#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")

# Running tests with previously built docker image
echo "Running tests with symfony's lime testing framework"
docker run -it --rm --name symfony1 \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
    php:8-cli data/bin/symfony symfony:test --trace ${@}
