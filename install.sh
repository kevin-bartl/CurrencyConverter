#!/bin/sh
if [ ! -e ./.env ]; then
    cp .env.dist .env
fi

if [ ! -e ./composer.phar ]; then
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
fi
php composer.phar install
