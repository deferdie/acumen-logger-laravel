# Acumen Logs - Laravel Logger

This package makes it really easy to report exceptions and laravel logs to your Acumen Logs dashboard. If you do not already have an account you can register for a free acount here: https://acumenlogs.com/register

## Install composer package

```
composer require deferdie/acumen-logger-laravel --no-interaction
```

## Add .env variables

```
ACUMEN_PROJECT_ID=""

ACUMEN_PROJECT_SECRET=""
```

## Register the service provider within the "providers" array in the config/app.php file

```
\AcumenLogger\AcumenLoggerServiceProvider::class
```

## Publish the config file

```
php artisan vendor:publish --provider="AcumenLogger\AcumenLoggerServiceProvider"
```

## Update app/Exceptions/Handler.php

```
public function report(Throwable $e)
{
    app(AcumenLogger::class)->handleException($e);
    return parent::report($e);
}
```

## Ignoring exceptions

You can ignore exceptions by adding its fully qualified class in the `ignore_exceptions` array located in `config/acumen.php`
