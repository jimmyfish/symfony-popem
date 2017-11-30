Symfony Popem Main Project
========================

Welcome to Popem project with Symfony v3.3

Before you go, please install necessary package from composer

```
composer install -vvv
```

Clear cache before start

```
php bin/console cache:clear --no-warmup
```

And don't forget to configure the `app/config/parameters.yml` and specify the right credential for Database

After configuration you must update the database structure for necessary project sequence

```
php bin/console doctrine:schema:update --force
```

Workflow
========

Before you go do the following :

``
git pull --rebase
``

to make sure your work is up-to-date with main project

if necessary do ` composer update `, and ` composer run-script post-install-cmd `