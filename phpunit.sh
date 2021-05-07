#!/bin/bash

VERSION=${VERSION:-5.6}
PHPUNIT=hillliu/pmvc-phpunit:${VERSION}

DIR="$( cd "$(dirname "$0")" ; pwd -P )"
PLUGIN_NAME="controller"


case "$1" in
  bash)
    docker run --rm -it\
      -v $DIR:/var/www/html \
      -v $DIR:/var/www/${PLUGIN_NAME} \
      --name phpunit ${PHPUNIT} \
      bash
    ;;

  *)
    docker run --rm \
      -v $DIR:/var/www/html \
      -v $DIR:/var/www/${PLUGIN_NAME} \
      --name phpunit ${PHPUNIT} \
      phpunit
    ;;
esac

exit $?
