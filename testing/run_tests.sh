#!/bin/sh

OLDPWD=$(pwd)
cd $(dirname "$0")
phpunit --verbose $@
cd $OLDPWD
