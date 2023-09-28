<?php

namespace AcumenLogger\Loggers;

class ExceptionLogger extends Logger
{
	private $statusCode;
	private $message;
	private $trace;
	private $line;
	

	public function __construct(\Exception $e)
	{
		try {
			
			parent::__construct();

			$this->statusCode = $e->getStatusCode();
			$this->message = $e->getMessage();
			$this->trace = $e->getTrace();
			$this->line = $e->getLine();
		} catch (\Exception $e) {
			dd($e);
		}
	}

	public function report()
	{
		return array_merge($this->getBaseProperties(), [
			'status_code' => $this->statusCode,
			'message' => $this->message,
			'trace' => $this->trace,
			'line' => $this->line,
		]);
	}
}