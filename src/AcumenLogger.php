<?php

namespace AcumenLogger;

use AcumenLogger\Exceptions\AcumenEnvironmentVariablesNotSet;
use AcumenLogger\Loggers\ExceptionLogger;


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

    public function __construct()
    {
        $this->checkEnvironmentVariablesAreSet();

        $this->projectId = env('ACUMEN_PROJECT_ID', false);
        $this->projectSecret = env('ACUMEN_PROJECT_SECRET', false);
    }

    /**
     * Check if the projects env variables are set.
     *
     * @return void
     */
    private function checkEnvironmentVariablesAreSet()
    {
        if (env('ACUMEN_PROJECT_ID', false) || env('ACUMEN_PROJECT_SECRET', false)) {
            throw new AcumenEnvironmentVariablesNotSet;
        }
    }

    public function handleException(\Exception $e)
    {
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
        $url = 'http://acumenlogs.com/beacon/logger/laravel';

        $data = [
            'exception' => $exception->report(),
            'project_id' => $this->projectId,
            'project_secret' => $this->projectSecret,
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            dd(curl_errno($ch));
        }

        // Close cURL session
        curl_close($ch);
    }
}
