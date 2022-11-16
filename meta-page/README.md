### Meta Pages

- [Installation](#installation)

### Installation

Install the package via Composer:

```
composer require jamstackvietnam/meta-pages
```

Publish the migration file with:

```
php artisan vendor:publish --provider="JamstackVietnam\MetaPage\ServiceProvider" --tag="migrations"
```

After the migration has been published you can create the `meta-pages` table by running:

```
php artisan migrate
```
