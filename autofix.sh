#!/usr/bin/env bash


php-cs-fixer fix --rules=@Symfony -vvv --no-interaction src/AppBundle

~/.config/composer/vendor/bin/phpcbf src/ -n -p --standard=PSR2 --ignore=*Test.php --ignore=*/Entity/* --ignore=*/js/libs/*

~/.config/composer/vendor/bin/phpcs src --ignore-annotations --ignore=src/AppBundle/Entity --standard=PSR2