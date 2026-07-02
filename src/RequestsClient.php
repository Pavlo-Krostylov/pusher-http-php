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
    private array $guzzleOptions;

    public function __construct(array $guzzleOptions = [])
    {
        $this->guzzleOptions = $guzzleOptions;
    }

    public function get($path, $options)
    {
        return $this->request('GET', $path, $options);
    }

    public function post($path, $options)
    {
        return $this->request('POST', $path, $options);
    }

    private function request($method, $path, $options)
    {
        $this->body = null;
        $this->statusCode = null;
        $url = $options['base_uri'] . '/' . $path;

        // ignore_errors: without it, file_get_contents() discards the response
        // body on any non-2xx status, so API error messages get lost.
        $opts = ['http' => ['method' => $method, 'ignore_errors' => true]];
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
        if (!empty($options['timeout'])) {
            $opts['http']['timeout'] = $options['timeout'];
        }

        $context = stream_context_create($opts);

        error_clear_last();
        $this->body = @file_get_contents($url, false, $context);
        $headers = function_exists('http_get_last_response_headers')
            ? (http_get_last_response_headers() ?? [])
            : ($http_response_header ?? []);

        if (!empty($headers[0])) {
            $status_line = $headers[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
            $this->statusCode = isset($match[1]) && is_numeric($match[1]) ? intval($match[1]) : null;
        }

        if ($this->body === false && empty($headers)) {
            // No HTTP response was ever received (DNS/connection/TLS failure or timeout).
            // Surface a real diagnostic instead of an empty, code-0 ApiErrorException.
            $error = error_get_last();
            $this->statusCode = 0;
            $this->body = $error['message'] ?? 'Unknown connection error';
        }

        return $this;
    }

    private function getClient() {
        if (is_null($this->client)) {
            $this->client = new \GuzzleHttp\Client($this->guzzleOptions);
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
