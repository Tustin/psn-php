<?php

use GuzzleHttp\Psr7\Response;
use Tustin\PlayStation\Exceptions\AccessDeniedHttpException;
use Tustin\PlayStation\Exceptions\ApiException;
use Tustin\PlayStation\Exceptions\NotFoundHttpException;
use Tustin\PlayStation\Exceptions\UnauthorizedHttpException;
use Tustin\PlayStation\Exceptions\UnsupportedMediaTypeHttpException;
use Tustin\PlayStation\Http\JsonStream;
use Tustin\PlayStation\Http\Middleware\ResponseHandlerMiddleware;

it('wraps successful responses with JsonStream', function (): void {
    $middleware = new ResponseHandlerMiddleware();
    $response = new Response(200, [], json_encode(['ok' => true]));

    $handled = $middleware($response);

    expect($handled->getBody())->toBeInstanceOf(JsonStream::class);
    expect($handled->getBody()->jsonSerialize()->ok)->toBeTrue();
});

it('throws ApiException for 400 responses', function (): void {
    $middleware = new ResponseHandlerMiddleware();
    $response = new Response(400, [], '{"error":"bad"}');

    $middleware($response);
})->throws(ApiException::class);

it('throws UnauthorizedHttpException for 401 responses', function (): void {
    $middleware = new ResponseHandlerMiddleware();
    $response = new Response(401, [], '{}');

    $middleware($response);
})->throws(UnauthorizedHttpException::class);

it('throws AccessDeniedHttpException for 403 responses', function (): void {
    $middleware = new ResponseHandlerMiddleware();
    $response = new Response(403, [], '{}');

    $middleware($response);
})->throws(AccessDeniedHttpException::class);

it('throws NotFoundHttpException for 404 responses', function (): void {
    $middleware = new ResponseHandlerMiddleware();
    $response = new Response(404, [], '{}');

    $middleware($response);
})->throws(NotFoundHttpException::class);

it('throws UnsupportedMediaTypeHttpException for 415 responses', function (): void {
    $middleware = new ResponseHandlerMiddleware();
    $response = new Response(415, [], '{}');

    $middleware($response);
})->throws(UnsupportedMediaTypeHttpException::class);
