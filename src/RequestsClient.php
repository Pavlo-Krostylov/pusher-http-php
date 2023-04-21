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

    public function get($path, $options)
    {
        $url = $options['base_uri'].'/'.$path;

        $opts = ['http' => ['method'  => 'GET']];
        if(!empty($options['query'])) {
            $url .= '?'.http_build_query($options['query']);
        }

        if(!empty($options['headers'])) {
            $opts['http']['header'] = "";
            foreach ($options['headers'] as $key => $value) {
                $opts['http']['header'] .= $key.": ".$value."\r\n";
            }
        }
        if(isset($options['body']) && $options['body'] != '' && $options['body'] != null) { $opts['http']['content'] = $options['body']; }

        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        return $result;
    }

    public function post($path, $options)
    {
        $url = $options['base_uri'].'/'.$path;

        $opts = ['http' => ['method'  => 'POST']];
        if(!empty($options['query'])) {
            $url .= '?'.http_build_query($options['query']);
        }

        if(!empty($options['headers'])) {
            $opts['http']['header'] = "";
            foreach ($options['headers'] as $key => $value) {
                $opts['http']['header'] .= $key.": ".$value."\r\n";
            }
        }
        if(isset($options['body']) && $options['body'] != '' && $options['body'] != null) { $opts['http']['content'] = $options['body']; }

        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        return $result;
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
