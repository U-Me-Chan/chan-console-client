<?php

namespace PF\Backend;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Requestor
{
    public const FETCH_BOARD = 'FETCH_BOARD';
    public const FETCH_THREAD = 'FETCH_THREAD';
    public const CREATE_POST = 'CREATE_POST';

    private const BASE_URI = 'http://pissykaka.scheoble.ml';

    /** @var Client */
    private $client;

    /** @var ResponseInterface|null */
    private $response = null;

    /**
     * Requestor constructor
     */
    public function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URI, 'timeout' => 10]);
    }

    /**
     * Выполняет запрос к API внешнего ресурса
     *
     * @param string $operation Тип запроса, указываемой одной из констант
     * @param array  $args      Список агрументов запроса
     *
     * @throws RuntimeException
     *
     * @return self
     */
    public function __invoke(string $operation, ...$args): self
    {
        switch ($operation) {
            case self::FETCH_BOARD:
                $this->makeRequest('GET', sprintf('/board/%s', $args[0]));

                if ($this->response->getStatusCode() == 204) {
                    throw new \OutOfBoundsException();
                }

                if ($this->response->getStatusCode() !== 200) {
                    throw new \RuntimeException();
                }

                break;
            case self::FETCH_THREAD:
                $this->makeRequest('GET', sprintf('/post/%d', $args[0]));

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

    /**
     * Возвращает данные ответа
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function getResponse(): array
    {
        $json = (string) $this->response->getBody();
        $result = json_decode($json, true);

        if ($result['error'] !== null) {
            throw new \RuntimeException();
        }

        return $result['payload'];
    }

    private function makeRequest(string $method, string $path, array $params = [])
    {
        $this->response = $this->client->request($method, $path, $params);
    }
}
