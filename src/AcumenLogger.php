<?php

namespace AcumenLogger;

use AcumenLogger\Loggers\ExceptionLogger;


class AcumenLogger
{
	protected $projectId;
	protected $projectSecret;

	public function __construct(\Exception $e)
	{
		$this->projectId=env('ACUMEN_PROJECT_ID');
		$this->projectSecret=env('ACUMEN_PROJECT_SECRET');
		
		$this->checkEnvironmentVariablesAreSet();
		$this->handleException($e);
	}
	
	private function checkEnvironmentVariablesAreSet()
	{
		if(!isset($this->projectId) || !isset($this->projectSecret)) {
			throw new \Exception('Please set the ACUMEN_PROJECT_ID and ACUMEN_PROJECT_SECRET');
		}
	}

	public function handleException(\Exception $e)
	{
		$exception = new ExceptionLogger($e);
		
		$this->dispatch($exception);
	}

	private function dispatch(ExceptionLogger $exception)
	{
		$url = 'http://acumen-nginx/beacon/logger/laravel';
		
		$data = [
			'exception' => $exception->report(),
			'project_id'=> $this->projectId,
			'project_secret'=> $this->projectSecret,
		];

		$ch = curl_init($url);

		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		

		// Execute cURL request
		$response = curl_exec($ch);

		// Check for errors
		if (curl_errno($ch)) {
			dd(curl_errno($ch));
		}
		
		// Close cURL session
		curl_close($ch);

		return json_decode($response, true);
	}
}
