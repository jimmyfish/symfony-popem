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
