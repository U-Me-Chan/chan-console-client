<?php

namespace PF\Backend;

use GuzzleHttp\Client;

class Requestor
{
    public const FETCH_BOARD = 'FETCH_BOARD';
    public const FETCH_THREAD = 'FETCH_THREAD';
    public const CREATE_POST = 'CREATE_POST';

    private const BASE_URI = 'http://pissykaka.ritsuka.host';

    private $client;
    private $response = null;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URI, 'timeout' => 10]);
    }

    public function __invoke(string $operation, ...$args): self
    {
        switch ($operation) {
            case self::FETCH_BOARD:
                $this->makeRequest('GET', '/board', ['query' => $args[0]]);

                if ($this->response->getStatusCode() !== 200) {
                    throw new \RuntimeException();
                }

                break;
            case self::FETCH_THREAD:
                $this->makeRequest('GET', '/post', ['query' => $args[0]]);

                if ($this->response->getStatusCode() !== 200) {
                    throw new \RuntimeException();
                }

                break;
            case self::CREATE_POST:
                $this->makeRequest('POST', '/post', ['form_params' => $args[0]]);

                if ($this->response->getStatusCode() !== 201) {
                    throw new \RuntimeException();
                }
                
                break;
            default:
                throw new \RuntimeException();
        }

        return $this;
    }

    public function getResponse(): array
    {
        $json = (string) $this->response->getBody();
        $result = json_decode($json, true);

        if ($result['error'] !== null) {
            throw new \RuntimeException();
        }

        return $result['payload'];
    }

    private function makeRequest(string $method, string $path, array $params)
    {
        $this->response = $this->client->request($method, $path, $params);
    }
}
