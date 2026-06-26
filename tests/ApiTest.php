<?php

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Enums\GraphqlOperation;
use Tustin\PlayStation\Exceptions\GraphQLRequestException;
use Tustin\PlayStation\Http\JsonStream;

it('sends graphql query operations as GET requests', function (): void {
    [$httpClient, $history] = $this->mockGuzzleClient([
        $this->jsonResponse([
            'data' => [
                'result' => 'ok',
            ],
            'errors' => null,
        ]),
    ]);

    $api = new Api($httpClient);
    $result = $api->graphql(GraphqlOperation::MetGetConceptById, ['conceptId' => 'ABC123']);

    expect($result->result)->toBe('ok');
    expect($history->entries)->toHaveCount(1);
    expect($history->entries[0]['request']->getMethod())->toBe('GET');
    expect((string) $history->entries[0]['request']->getUri())->toContain('graphql/v1/op?');
});

it('sends graphql mutation operations as POST requests', function (): void {
    [$httpClient, $history] = $this->mockGuzzleClient([
        $this->jsonResponse([
            'data' => [
                'ok' => true,
            ],
            'errors' => null,
        ]),
    ]);

    $api = new Api($httpClient);
    $result = $api->graphql(GraphqlOperation::AddToCart, [
        'skus' => [
            ['skuId' => 'SKU'],
        ],
    ]);

    expect($result->ok)->toBeTrue();
    expect($history->entries)->toHaveCount(1);
    expect($history->entries[0]['request']->getMethod())->toBe('POST');
});

it('throws graphql request exception when response has errors', function (): void {
    [$httpClient] = $this->mockGuzzleClient([
        $this->jsonResponse([
            'errors' => [
                ['message' => 'Something went wrong.'],
            ],
        ]),
    ]);

    $api = new Api($httpClient);

    $api->graphql(GraphqlOperation::MetGetConceptById, []);
})->throws(GraphQLRequestException::class, 'GraphQL operation failed: Something went wrong.');

it('decodes GET and POST request payloads', function (): void {
    [$httpClient] = $this->mockGuzzleClient([
        $this->jsonResponse(['value' => 1]),
        $this->jsonResponse(['value' => 2]),
    ]);

    $api = new Api($httpClient);

    expect($api->get('endpoint')->value)->toBe(1)
        ->and($api->post('endpoint', ['foo' => 'bar'])->value)->toBe(2);
});

it('middleware wraps successful response body in JsonStream', function (): void {
    [$client] = $this->mockClient([
        $this->jsonResponse([
            'value' => 'wrapped',
        ]),
    ]);

    $response = $client->getHttpClient()->get('wrapped-endpoint');

    expect($response->getBody())->toBeInstanceOf(JsonStream::class);
    expect($response->getBody()->jsonSerialize()->value)->toBe('wrapped');
});
