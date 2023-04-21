<?php

namespace Pusher;

class RequestsClient
{
    /**
     * @var null|resource
     */
    private $client = null; // Guzzle client
    private $statusCode = null;
    private $body = null;

    public function __construct()
    {
    }

    public function get($path, $options)
    {
        $this->body = null;
        $this->statusCode = null;
        $url = $options['base_uri'] . '/' . $path;

        $opts = ['http' => ['method' => 'GET']];
        if (!empty($options['query'])) {
            $url .= '?' . http_build_query($options['query']);
        }

        if (!empty($options['headers'])) {
            $opts['http']['header'] = "";
            foreach ($options['headers'] as $key => $value) {
                $opts['http']['header'] .= $key . ": " . $value . "\r\n";
            }
        }
        if (isset($options['body']) && $options['body'] != '' && $options['body'] != null) {
            $opts['http']['content'] = $options['body'];
        }

        $context = stream_context_create($opts);

        @$this->body = file_get_contents($url, false, $context);
        if (!empty($http_response_header[0])) {
            $status_line = $http_response_header[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
            $this->statusCode = $match[1];
            if(is_numeric($this->statusCode)) {
                $this->statusCode = intval($this->statusCode);
            }
        }

        return $this;
    }

    public function post($path, $options)
    {
        $this->body = null;
        $this->statusCode = null;
        $url = $options['base_uri'] . '/' . $path;

        $opts = ['http' => ['method' => 'POST']];
        if (!empty($options['query'])) {
            $url .= '?' . http_build_query($options['query']);
        }

        if (!empty($options['headers'])) {
            $opts['http']['header'] = "";
            foreach ($options['headers'] as $key => $value) {
                $opts['http']['header'] .= $key . ": " . $value . "\r\n";
            }
        }
        if (isset($options['body']) && $options['body'] != '' && $options['body'] != null) {
            $opts['http']['content'] = $options['body'];
        }

        $context = stream_context_create($opts);

        @$this->body = file_get_contents($url, false, $context);
        if (!empty($http_response_header[0])) {
            $status_line = $http_response_header[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
            $this->statusCode = $match[1];
            if(is_numeric($this->statusCode)) {
                $this->statusCode = intval($this->statusCode);
            }
        }

        return $this;
    }

    private function getClient() {
        if (is_null($this->client)) {
            $this->client = new \GuzzleHttp\Client();
        }
        return $this->client;
    }

    public function postAsync($path, $options)
    {
        return $this->getClient()->postAsync($path, $options);
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getBody() {
        return $this->body;
    }
}
