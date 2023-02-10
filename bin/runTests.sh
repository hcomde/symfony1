#!/bin/bash
set -e

SCRIPT_DIR=$(dirname "$(readlink -e "$0")")

# Running composer install
docker run -it --rm --tty --name symfony1_composer \
  -v "${SCRIPT_DIR}/../:/app" \
    composer:2.2 install --optimize-autoloader

./symfonyTests.sh
./phpunitTests.sh
