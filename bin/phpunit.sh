#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")

# Building docker image with xdebug for development
echo "Building php cli docker image with xdebug"
docker build -q -t php:8-cli-xdebug "${SCRIPT_DIR}/docker/php"

# Running tests with previously built docker image
echo "Running tests with phpunit testing framework"
docker run -it --rm --name symfony1 \
  --add-host=host.docker.internal:host-gateway \
  -e XDEBUG_CONFIG="idekey=PHPSTORM" \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
    php:8-cli-xdebug vendor/bin/phpunit /usr/src/symfony1/test/phpunit ${@}
