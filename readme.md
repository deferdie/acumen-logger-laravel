# Acumen Logs - Laravel Logger

Welcome to Acumen Logs! This Laravel package makes it easy to report exceptions and Laravel logs to your Acumen Logs dashboard. If you don't have an account yet, you can sign up for a free one [here](https://acumenlogs.com/register).

## Getting Started

Follow these steps to get Acumen Logs up and running in your Laravel project:

### Step 1: Install the Composer Package

Run the following command in your terminal:

```bash
composer require deferdie/acumen-logger-laravel --no-interaction
```

### Step 2: Update Your .env File

```
ACUMEN_PROJECT_ID=""

ACUMEN_PROJECT_SECRET=""
```

### Step 3: Register the service provider

Add the following line to the "providers" array in your `config/app.php` file:

```
\AcumenLogger\AcumenLoggerServiceProvider::class
```

### Step 4: Publish the Config file

Run the following command in your terminal:

```
php artisan vendor:publish --provider="AcumenLogger\AcumenLoggerServiceProvider"
```

### Step 5: Update Your Exception Handler

Update the report method in your app/Exceptions/Handler.php file to look like this:

```
public function report(Throwable $e)
{
    app(AcumenLogger::class)->handleException($e);
    return parent::report($e);
}
```

## Optional: Ignoring Exceptions

If you want to ignore certain exceptions, add their fully qualified class names to the ignore_exceptions array in your config/acumen.php file.

That's it! You're now ready to start using Acumen Logs in your Laravel project.
