<?php

namespace Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Http\JsonStream;
use Tustin\PlayStation\Http\Middleware\ResponseHandlerMiddleware;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates a PlayStation client backed by a Guzzle MockHandler.
     *
     * @param array<int, Response> $responses
     * @return array{0: Client, 1: object}
     */
    protected function mockClient(array $responses): array
    {
        $mockHandler = new MockHandler($responses);
        $history = new class {
            /** @var array<int, array{request: mixed, options: array<string, mixed>}> */
            public array $entries = [];
        };

        $historyStack = HandlerStack::create($mockHandler);
        $historyStack->push(Middleware::mapResponse(new ResponseHandlerMiddleware()));
        $historyStack->push(Middleware::tap(function ($request, array $options) use ($history): void {
            $history->entries[] = [
                'request' => $request,
                'options' => $options,
            ];
        }));

        return [
            new Client([
                'handler' => $historyStack,
            ]),
            $history,
        ];
    }

    /**
     * Creates a plain Guzzle client backed by a Guzzle MockHandler.
     *
     * @param array<int, Response> $responses
     * @return array{0: GuzzleClient, 1: object}
     */
    protected function mockGuzzleClient(array $responses): array
    {
        $mockHandler = new MockHandler($responses);
        $history = new class {
            /** @var array<int, array{request: mixed, options: array<string, mixed>}> */
            public array $entries = [];
        };

        $historyStack = HandlerStack::create($mockHandler);
        $historyStack->push(Middleware::tap(function ($request, array $options) use ($history): void {
            $history->entries[] = [
                'request' => $request,
                'options' => $options,
            ];
        }));

        return [
            new GuzzleClient([
                'handler' => $historyStack,
                'http_errors' => false,
                'base_uri' => 'https://example.com/api/',
            ]),
            $history,
        ];
    }

    /**
     * Creates a JSON response for queued mock handlers.
     */
    protected function jsonResponse(mixed $data = null, int $status = 200, array $headers = []): Response
    {
        $body = $data === null ? '' : json_encode($data, JSON_THROW_ON_ERROR);
        $response = new Response($status, $headers, $body);

        return $response->withBody(new JsonStream($response->getBody()));
    }

    protected function tearDown(): void
    {
        Client::resetInstance();

        parent::tearDown();
    }
}
