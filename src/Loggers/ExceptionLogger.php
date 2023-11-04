<?php

namespace AcumenLogger\Loggers;

use Exception;

class ExceptionLogger extends Logger
{
    private $statusCode;
    private $message;
    private $trace;
    private $line;
    private $exception_name;


    public function __construct(\Exception $e)
    {
        try {
            parent::__construct();
            $this->statusCode = $e->getCode();
            $this->message = $e->getMessage();
            $this->trace = $e->getTrace();
            $this->line = $e->getLine();
            $this->exception_name = get_class($e);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function report()
    {
        try {
            return array_merge($this->getBaseProperties(), [
                'exception_name' => $this->exception_name,
                'status_code' => $this->statusCode,
                'message' => $this->message,
                'trace' => $this->trace,
                'line' => $this->line,
                'headers' => request()->header(),
                'body' => request()->post(),
                'route_data' => request()->route()
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }
}
