<?php

namespace AcumenLogger\Loggers;

use AcumenLogger\AcumenPerformance;
use COM;
use Exception;
use ReflectionClass;

class ExceptionLogger extends Logger
{
    private $statusCode;
    private $message;
    private $trace;
    private $line;
    private $exception_name;
    private $php_severity;
    private $exception_namespace;

    public function __construct($e)
    {
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

    public function getServerLoadTime()
    {

        try {
            if (stristr(PHP_OS, 'win')) {

                $wmi = new COM("Winmgmts://");
                $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");

                $cpu_num = 0;
                $load_total = 0;

                foreach ($server as $cpu) {
                    $cpu_num++;
                    $load_total += $cpu->loadpercentage;
                }

                $load = round($load_total / $cpu_num);
            } else {

                $sys_load = sys_getloadavg();
                $load = $sys_load[0];
            }

            return (int) $load;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function report()
    {
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
                'server_load' => $this->getServerLoadTime()
            ]);
        } catch (\Exception $e) {
            // dd($e);
        }
    }
}
