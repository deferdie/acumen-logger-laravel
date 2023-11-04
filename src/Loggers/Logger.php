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

    private function parseException()
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
    private function setRequestUri()
    {
        $this->pathName = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

        return $this;
    }

    /**
     * Sets the Host.
     *
     * @return Logger
     */
    public function setHost()
    {
        $this->host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

        return $this;
    }

    /**
     * Sets the request Port.
     *
     * @return Logger
     */
    private function setPort()
    {
        $this->port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;

        return $this;
    }

    /**
     * Set the HTTP user-agent.
     *
     * @return Logger
     */
    private function setHTTPUserAgent()
    {
        $this->HTTPUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        return $this;
    }

    /**
     * Sets the Env.
     *
     * @return Logger
     */
    public function setEnv()
    {
        $this->env = env('APP_ENV');

        return $this;
    }

    /**
     * Set the HTTP method.
     *
     * @return Logger
     */
    private function setHTTPMethod()
    {
        $this->HTTPMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

        return $this;
    }

    /**
     * Sets the request query parameters.
     *
     * @return Logger
     */
    public function setQueryParameters()
    {
        $this->queryParameters = request()->query();

        return $this;
    }

    /**
     * Sets the Remote Address.
     *
     * @return Logger
     */
    public function setRemoteAddress()
    {
        $this->remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        return $this;
    }

    /**
     * Sets the Referer.
     *
     * @return Logger
     */
    public function setReferer()
    {
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        return $this;
    }

    /**
     * Gets the request URI.
     *
     * @return string
     */
    private function getRequestUri()
    {
        return $this->pathName;
    }

    /**
     * Gets the request Host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Gets the request Host.
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get the HTTP user-agent.
     *
     * @return string
     */
    private function getHTTPUserAgent()
    {
        return $this->HTTPUserAgent;
    }

    /**
     * Gets the request Env.
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Get the HTTP method.
     *
     * @return string
     */
    private function getHTTPMethod()
    {
        return $this->HTTPMethod;
    }

    /**
     * Get the request query parameters.
     *
     * @return string
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * Get the Remote Address.
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * Get the Referer.
     *
     * @return string
     */
    public function getReferer()
    {
        return is_null($this->referer) ? '' : $this->referer;
    }

    /**
     * Gets the request Url.
     *
     * @return string
     */
    public function getUrl()
    {
        return url()->current();
    }

    /**
     * Returns core parameters
     *
     * @return array
     */
    public function getBaseProperties()
    {
        try {
            return [
                'path_name' => $this->getRequestUri(),
                'host' => $this->getHost(),
                'port' => $this->getPort(),
                'http_user_agent' => $this->getHTTPUserAgent(),
                'env' => $this->getEnv(),
                'request_method' => $this->getHTTPMethod(),
                'query_params' => $this->getQueryParameters(),
                'remote_address' => $this->getRemoteAddress(),
                'referer' => $this->getReferer(),
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
            ];
        } catch (\Exception $e) {
            // dd($e);
        }
    }
}
