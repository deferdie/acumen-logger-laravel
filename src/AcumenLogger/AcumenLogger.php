<?php

namespace AcumenLogger;

use Exception;
use AcumenLogger\Loggers\ExceptionLogger;
use AcumenLogger\Exceptions\AcumenEnvironmentVariablesNotSet;

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
     */
    public $sqlQueries = [];

    /**
     * The logs for the current request.
     */
    public $logs = [];

    /**
     * The events for the current app.
     */
    public $events = [];

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
    public function setSqlQuery(array $query)
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

        $exception = new ExceptionLogger($e);

        $this->dispatch($exception);
    }

    /**
     * Dispatch the log to the beacon.
     *
     * @param \AcumenLogger\Loggers\ExceptionLogger $exception
     * @return void
     */
    private function dispatch(ExceptionLogger $exception)
    {
        try {
            $url = 'https://acumenlogs.com/api/beacon/logger/laravel';

            $data = [
                'project_id' => $this->projectId,
                'project_secret' => $this->projectSecret,
                'exception' => json_encode($exception->report()),
                'sql' => json_encode($this->sqlQueries),
                'events' => json_encode($this->events),
                'logs' => json_encode($this->logs),
            ];

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                // curl_errno($ch);
            }

            // Close cURL session
            curl_close($ch);
        } catch (Exception $e) {
        }
    }
}
