[![Latest Version on Packagist](https://img.shields.io/packagist/v/7Lab/laravel-remote-logging.svg?style=flat-square)](https://packagist.org/packages/7Lab/laravel-remote-logging)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/7Lab/laravel-remote-logging.svg?style=flat-square)](https://packagist.org/packages/7Lab/laravel-remote-logging)

# Cache responses
This Laravel package send the errors or failed jobs occuring in your application to an external server. 
For example to display these errors or failed jobs on a dashboard like the [spatie/dashboard.spatie.be](https://github.com/spatie/dashboard.spatie.be).

For the failed jobs logging we are extending [spatie/laravel-failed-job-monitor](https://github.com/spatie/laravel-failed-job-monitor)  and overwriting their default Notification class to allow us to send it to your remote server.

## Installation
You can install the package via Composer:
```bash
composer require 7Lab/laravel-remote-logging
```

The package will automatically register itself.

You can publish the config file with:
```bash
php artisan vendor:publish --provider="SevenLab\RemoteLogging\RemoteLoggingServiceProvider"
```

This is the contents of the published config file (`config/remote-logging.php`).
```

And finally you should install the exception handling in the `report` function in the Exception handler (`app/Exceptions/Handler.php`). 
```php
...

if (app()->bound('remote-logging')) {
    app('remote-logging')->captureException($exception);
}

...
```

## Usage
By default it will send all errors and failed jobs that occur into your application to the remote server specified in the config file.


## Credits
- [Niels Kramer](https://github.com/nielskramerr)
- [Joey Houtenbos](https://github.com/JoeyHoutenbos)
- [All Contributors](https://github.com/7lab/laravel-remote-logging/contributors)