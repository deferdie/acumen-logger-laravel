<?php

namespace AcumenLogger\Loggers;

use AcumenLogger\Performance;
use ReflectionClass;
use Ramsey\Uuid\Uuid;

class ExceptionLogger extends Logger
{
    private $statusCode;
    private $message;
    private $trace;
    private $line;
    private $exception_name;
    private $php_severity;
    private $exception_namespace;
    private $exception;

    public function __construct($e)
    {
        $this->exception = $e;
        try {
            parent::__construct();
            $this->statusCode = $e->getCode();
            $this->message = $e->getMessage();
            $this->line = $e->getLine();
            $this->exception_name = get_class($e);
            $this->php_severity = method_exists($e, 'getSeverity') ?  $e->getSeverity() : null;
            $this->exception_namespace = (new ReflectionClass($e))->getNamespaceName();
            $this->setTrace($e->getTrace());
        } catch (\Exception $e) {
            // dd($e);
        }
    }

    /**
     * Parse the trace and get the file data.
     *
     * @param array $traces
     * @return void
     */
    private function setTrace($traces = [])
    {
        $trace = [];

        foreach ($traces as $traceToCheck) {
            $file = new \SplFileObject($traceToCheck['file'], 'r');
            $file->seek(PHP_INT_MAX);
            $lines = new \LimitIterator($file, $traceToCheck['line'] - 15, 25);
            $arr = iterator_to_array($lines);
            $traceToCheck['file_data'] = $arr;
            array_push($trace, $traceToCheck);
        }

        $this->trace = $trace;
    }

    /**
     * Get the user id if the user is authenticated
     *
     * @return int|null
     */
    private function getUserId()
    {
        if (auth()->check()) {
            return auth()->user()->id;
        }

        return null;
    }

    /**
     * Get the HTTP status code based on the current exception.
     *
     * @return array
     */
    private function getStatusCode()
    {
        if (method_exists($this->exception, 'getStatusCode')) {
            return $this->exception->getStatusCode();
        }

        return 500;
    }


    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        $uuid = Uuid::uuid4();

        try {
            return array_merge($this->getBaseProperties(), [
                'exception_name' => $this->exception_name,
                'status_code' => $this->statusCode,
                'message' => $this->message,
                'exception_namespace' => $this->exception_namespace,
                'php_severity' => $this->php_severity,
                'exception_trace' => json_encode($this->trace),
                'line' => $this->line,
                'headers' => json_encode(request()->header()),
                'body' => json_encode(request()->post()),
                'route_data' => json_encode(request()->route()),
                'authenticated' => auth()->check(),
                'php_memory_useage' => memory_get_usage(),
                'user_id' => $this->getUserId(),
                'request_id' => $uuid->toString(),
                'http_status_code' => $uuid->getStatusCode(),
            ]);
        } catch (\Exception $e) {
            // dd($e);
        }
    }
}
