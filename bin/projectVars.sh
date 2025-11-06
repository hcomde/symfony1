#!/bin/bash
set -e

export COMPOSER_TAG="2.8.12"
export PHP_VERSION="8.4"
export APCU_VERSION="5.1.27"
export MEMCACHE_VERSION="8.2"
export XDEBUG_VERSION="3.4.7"
export DOCKERFILE="php82_84"
export PHP_IMAGE_TAG="local/hcomde/symfony1/${DOCKERFILE}:${PHP_VERSION}"
