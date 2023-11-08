<?php

namespace AcumenLogger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class AcumenLoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function ($query) {
        });

        Event::listen('*', function ($event, $eventData) {

            $acumenLogger = app(AcumenLogger::class);

            $acumenLogger->addEvent($event);

            foreach ($eventData as $data) {
                if ($event === 'Illuminate\Database\Events\QueryExecuted') {
                    $logData = [
                        'time' => $data->time,
                        'query' => $data->sql,
                        'connectionName' => $data->connectionName,
                        'memory' => number_format(memory_get_usage()),
                    ];

                    $acumenLogger->setSqlQuery($logData);
                }

                if ($event === 'Illuminate\Log\Events\MessageLogged') {
                    $acumenLogger->addLogEntry($data);
                }
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(AcumenLogger::class, static function ($app) {
            return new AcumenLogger();
        });
    }
}
