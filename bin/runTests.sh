#!/bin/bash
SCRIPT_DIR=$(dirname "$(readlink -e "$0")")

docker run -it --rm --name symfony1 \
  -v "${SCRIPT_DIR}/../:/usr/src/symfony1" \
  -w "/usr/src/symfony1" \
    php:7.2 data/bin/symfony symfony:test --trace ${@}
