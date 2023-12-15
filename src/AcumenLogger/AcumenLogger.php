<?php

namespace AcumenLogger;

use Exception;
use AcumenLogger\Loggers\ExceptionLogger;
use AcumenLogger\Exceptions\AcumenEnvironmentVariablesNotSet;
use Illuminate\Support\Facades\Config;

class AcumenLogger
{
    /**
     * The Acumen project id.
     *
     * @var string
     */
    private $projectId;

    /**
     * The Acumen project secret.
     *
     * @var string
     */
    private $projectSecret;

    /**
     * The sql queries for the current request.
     *
     * @var array
     */
    public $sqlQueries = [];

    /**
     * The logs for the current request.
     *
     * @var array
     */
    public $logs = [];

    /**
     * The events for the current app.
     *
     * @var array
     */
    public $events = [];

    /**
     * Should the log be dispatched.
     *
     * @var bool
     */
    public $shouldDispatch = true;

    /**
     * The reported exception.
     */
    public $exception = null;

    public function __construct()
    {
        $this->checkEnvironmentVariablesAreSet();

        $this->projectId = env('ACUMEN_PROJECT_ID', false);
        $this->projectSecret = env('ACUMEN_PROJECT_SECRET', false);
    }

    /**
     * Check if the projects env variables are set.
     *
     * @throws \AcumenLogger\Exceptions\AcumenEnvironmentVariablesNotSet
     * @return void
     */
    private function checkEnvironmentVariablesAreSet()
    {
        if (env('ACUMEN_PROJECT_ID', false) === false || env('ACUMEN_PROJECT_SECRET', false) === false) {
            throw new AcumenEnvironmentVariablesNotSet;
        }
    }

    /**
     * Set an sql query.
     *
     * @return void
     */
    public function addSqlQuery(array $query)
    {
        array_push($this->sqlQueries, $query);
    }

    /**
     * Set a log entry.
     *
     * @return void
     */
    public function addLogEntry($entry)
    {
        array_push($this->logs, [
            'reported_at' => time(),
            'level' => $entry->level,
            'message' => $entry->message,
            'context' => $entry->context,
        ]);
    }

    /**
     * Add a new log.
     *
     * @return void
     */
    public function addEvent($event)
    {
        array_push($this->events, $event);
    }

    /**
     * Handle the exception.
     *
     * @param \Exception $e
     * @return void
     */
    public function handleException($e)
    {
        // Check if the exception should be ignored.
        if (in_array(get_class($e), config('acumen.ignore_exceptions'))) {
            return;
        }

        $this->exception = new ExceptionLogger($e);

        $this->dispatch($this->exception);
    }

    /**
     * Dispatch the log to the beacon.
     *
     * @param \AcumenLogger\Loggers\ExceptionLogger $exception
     * @return void
     */
    public function dispatch(ExceptionLogger $exception)
    {
        if (!$this->shouldDispatch) {
            return;
        }

        try {
            $data = [
                'project_id' => $this->projectId,
                'project_secret' => $this->projectSecret,
                'exception' => json_encode($exception->report()),
                'sql' => json_encode($this->sqlQueries),
                'events' => json_encode($this->events),
                'logs' => json_encode($this->logs),
            ];

            // Post the data via guzzle
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://acumenlogs.com/api/beacon/logger/laravel', [
                'form_params' => $data
            ]);
        } catch (Exception $e) {
        }
    }

    /**
     * Dispatches all the logs.
     *
     * @return void
     */
    public function reportLogs()
    {
        if (!$this->shouldDispatch) {
            return;
        }

        try {
            $data = [
                'project_id' => $this->projectId,
                'project_secret' => $this->projectSecret,
                'logs' => json_encode($this->logs),
            ];

            // Post the data via guzzle
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://acumenlogs.com/api/beacon/logger/laravel/logs', [
                'form_params' => $data
            ]);
        } catch (Exception $e) {
            // dd($e);
        }
    }
}
