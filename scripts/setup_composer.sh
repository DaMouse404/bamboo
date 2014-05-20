#!/bin/bash

# Run on composer.json
BASEDIR=$(dirname $0)/..
cd $BASEDIR

if [ ! -e "composer.phar" ]; then
    # Download composer.phar
    curl -sS http://getcomposer.org/installer | php -d detect_unicode=Off
fi


# Execute composer
COMPOSER=composer.json COMPOSER_HOME=.composer php -d disable_functions= composer.phar update

# Delete temp lock file for branch switch
rm composer.lock
