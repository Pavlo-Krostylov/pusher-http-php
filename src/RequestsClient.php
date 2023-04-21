<?php

namespace Pusher;

class RequestsClient
{
    /**
     * @var null|resource
     */
    private $client = null; // Guzzle client

    public function __construct()
    {

    }


    public function get(string $url, $headers = '', $content = '')
    {
        $opts = ['http' => ['method'  => 'GET']];

        if(!empty($headers)) { $opts['http']['header'] = $headers; }
        if($content != '' && $content != null) { $opts['http']['content'] = $content; }

        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    public function post(string $url, $headers = '', $content = '')
    {
        $opts = ['http' => ['method'  => 'POST']];

        if(!empty($headers)) { $opts['http']['header'] = $headers; }
        if($content != '' && $content != null) { $opts['http']['content'] = $content; }

        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
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
}
