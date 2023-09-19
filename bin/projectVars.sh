#!/bin/bash
set -e

export COMPOSER_BUILD_TAG="composer:2.2"
export PHP_VERSION="8.1"
export APCU_VERSION="5.1.22"
export MEMCACHE_VERSION="8.2"
export DOCKERFILE="php74_82"
export PHP_IMAGE_TAG="local/hcomde/symfony1/${DOCKERFILE}:${PHP_VERSION}"
