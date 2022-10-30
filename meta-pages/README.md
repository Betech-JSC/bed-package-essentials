### Meta Pages

- [Installation](#installation)

### Installation

Install the package via Composer:

```
composer require jamstackvietnam/meta-pages
```

Publish the migration file with:

```
php artisan vendor:publish --provider="Jamstackvietnam\MetaPages\ServiceProvider" --tag="migrations"

php artisan vendor:publish --provider="Jamstackvietnam\MetaPages\ServiceProvider" --tag="models"
```

After the migration has been published you can create the `meta-pages` table by running:

```
php artisan migrate
```
