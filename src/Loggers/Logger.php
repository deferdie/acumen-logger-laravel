<?php

namespace AcumenLogger\Loggers;

use App\class_lib\Logging\CloudWatchReporter;
use Illuminate\Support\Arr;

abstract class Logger
{
	private $url;
	private $host;
	private $port;
	private $pathName;
	private $HTTPUserAgent;
	private $env;
	private $HTTPMethod;
	private $queryParameters;
	private $remoteAddress;
	private $referer;

	public function __construct()
	{
		$this->parseException();
	}

	private function parseException(): void
	{
		$this->setRequestUri()
			->setHost()
			->setPort()
			->setHTTPUserAgent()
			->setEnv()
			->setHTTPMethod()
			->setQueryParameters()
			->setRemoteAddress()
			->setReferer();
	}

	/**
	 * Sets the request URI.
	 *
	 * @return Logger
	 */
	private function setRequestUri(): Logger
	{
		$this->pathName = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

		return $this;
	}

	/**
	 * Sets the Host.
	 *
	 * @return Logger
	 */
	public function setHost(): Logger
	{
		$this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

		return $this;
	}

	/**
	 * Sets the request Port.
	 *
	 * @return Logger
	 */
	private function setPort(): Logger
	{
		$this->port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;

		return $this;
	}

	/**
	 * Set the HTTP user-agent.
	 *
	 * @return Logger
	 */
	private function setHTTPUserAgent(): Logger
	{
		$this->HTTPUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		return $this;
	}

	/**
	 * Sets the Env.
	 *
	 * @return Logger
	 */
	public function setEnv(): Logger
	{
		$this->env = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : null;

		return $this;
	}

	/**
	 * Set the HTTP method.
	 *
	 * @return Logger
	 */
	private function setHTTPMethod(): Logger
	{
		$this->HTTPMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

		return $this;
	}

	/**
	 * Sets the request query parameters.
	 *
	 * @return Logger
	 */
	public function setQueryParameters(): Logger
	{
		$this->queryParameters = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;

		return $this;
	}

	/**
	 * Sets the Remote Address.
	 *
	 * @return Logger
	 */
	public function setRemoteAddress(): Logger
	{
		$this->remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

		return $this;
	}

	/**
	 * Sets the Referer.
	 *
	 * @return Logger
	 */
	public function setReferer(): Logger
	{
		$this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

		return $this;
	}

	/**
	 * Gets the request URI.
	 *
	 * @return string
	 */
	private function getRequestUri(): string
	{
		return $this->pathName;
	}

	/**
	 * Gets the request Host.
	 *
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * Gets the request Host.
	 *
	 * @return string
	 */
	public function getPort(): string
	{
		return $this->port;
	}

	/**
	 * Get the HTTP user-agent.
	 *
	 * @return string
	 */
	private function getHTTPUserAgent(): string
	{
		return $this->HTTPUserAgent;
	}

	/**
	 * Gets the request Env.
	 *
	 * @return string
	 */
	public function getEnv(): string
	{
		return $this->env;
	}

	/**
	 * Get the HTTP method.
	 *
	 * @return string
	 */
	private function getHTTPMethod(): string
	{
		return $this->HTTPMethod;
	}

	/**
	 * Get the request query parameters.
	 *
	 * @return string
	 */
	public function getQueryParameters(): string
	{
		return $this->queryParameters;
	}

	/**
	 * Get the Remote Address.
	 *
	 * @return string
	 */
	public function getRemoteAddress(): string
	{
		return $this->remoteAddress;
	}

	/**
	 * Get the Referer.
	 *
	 * @return string
	 */
	public function getReferer(): string
	{
		return is_null($this->referer) ? '' : $this->referer;
	}

	/**
	 * Gets the request Url.
	 *
	 * @return string
	 */
	public function getUrl(): string
	{
		return url()->current();
	}

	public function getBaseProperties()
	{
		return [
			'path_name' => $this->getRequestUri(),
			'host' => $this->getHost(),
			'port' => $this->getPort(),
			'http_user_agent' => $this->getHTTPUserAgent(),
			'env' => $this->getEnv(),
			'request_method' => $this->getHTTPMethod(),
			'query_param' => $this->getQueryParameters(),
			'remote_address' => $this->getRemoteAddress(),
			'referer' => $this->getReferer(),
		];
	}
}