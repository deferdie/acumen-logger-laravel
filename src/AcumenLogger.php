<?php

namespace AcumenLogger;

use AcumenLogger\Loggers\ExceptionLogger;

class AcumenLogger
{
    protected $projectId;

    protected $projectSecret;
	
	public function __construct(\Exception $e)
	{
		$this->handleException($e);
	}

	public function handleException(\Exception $e)
	{
		$exception = new ExceptionLogger($e);
		
		dd($exception->report());
//		$this->report():void
	}
		
}
