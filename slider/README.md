### Nested slider for Laravel

- [Installation](#installation)
- [Config](#config)
- [Setting](#setting)
- [Usage](#usage)

### Installation

`composer.json`
```
"require": {
    "jamstackvietnam/slider": "*"
},
"repositories": {
    {
        "type": "path",
        "url": "./packages/jamstackvietnam/slider"
    }
}
```
Install the package via Composer:

```
composer require jamstackvietnam/slider
```

Publish the migration file with:

```
php artisan vendor:publish --provider="Jamstackvietnam\Slider\ServiceProvider" --tag="migrations"
```

### Config

```bash
php artisan vendor:publish --provider="Jamstackvietnam\Slider\ServiceProvider" --tag="config"
```

### Setting
You can add another position of system to the config file.

See the configuration below:

```php
    'positions' => [
        'HOME_SLIDER' => [
            'title' => 'Slider trang chủ',
            'value' => 'HOME_SLIDER',
            'count' => 5,
        ],
        'BLOB' => [
            'title' => 'Banner bài viết',
            'value' => 'BLOB',
            'count' => 1,
        ],
    ],
```

After the migration has been published you can create the `sliders` table by running:

```
php artisan migrate
```
