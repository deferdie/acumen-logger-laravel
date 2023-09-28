<?php

namespace AcumenLogger\Loggers;


abstract class Logger
{
	private $url;
	private $host;
	private $port;
	private $pathName;
	private $userAgent;
	private $env;
	private $requestMethod;
	private $queryParam;
	private $remoteAddress;
	private $referer;


	public function __construct()
	{
		$this->host = $_SERVER['HTTP_HOST'];
		$this->port = $_SERVER['SERVER_PORT'];
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->env = $_SERVER['APP_ENV'];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		$this->queryParam = $_SERVER['QUERY_STRING'];
		$this->remoteAddress = $_SERVER['REMOTE_ADDR'];
//		$this->referer = $_SERVER['HTTP_REFERER'];

		$this->parseException();
	}

	private function parseException(): void
	{
		$this->setRequestUri();
	}

	/**
	 * Sets the request URI.
	 *
	 * @return Logger
	 */
	public function setRequestUri(): Logger
	{
		$this->pathName = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']: null;

		return $this;
	}

	public function getUrl()
	{
		return url()->current();
	}

	/**
	 * Gets the request URI.
	 *
	 * @return string
	 */
	public function getRequestUri(): string
	{
		return $this->pathName;
	}
	
	public function getBaseProperties()
	{
		return [
			'path_name' => $this->getRequestUri(),
			'host' => $this->host,
			'port' => $this->port,
			'user_agent' => $this->userAgent,
			'env' => $this->env,
			'request_method' => $this->requestMethod,
			'query_param' => $this->queryParam,
			'remote_address' => $this->remoteAddress,
			'referer' => $this->referer,
		];
	}
}