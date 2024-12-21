<?php

namespace Tustin\PlayStation\Http\Middleware;

use GuzzleHttp\Psr7\Response;

use Psr\Http\Message\StreamInterface;
use Tustin\PlayStation\Http\JsonStream;
use Tustin\PlayStation\Exceptions\ApiException;
use Tustin\PlayStation\Exceptions\NotFoundHttpException;
use Tustin\PlayStation\Exceptions\AccessDeniedHttpException;
use Tustin\PlayStation\Exceptions\UnauthorizedHttpException;
use Tustin\PlayStation\Exceptions\UnsupportedMediaTypeHttpException;

final class ResponseHandlerMiddleware
{
    /**
     * Handles all responses.
     */
    public function __invoke(Response $response, array $options = [])
    {
        $jsonStream = new JsonStream($response->getBody());

        if ($this->isSuccessful($response)) {
            return $response->withBody($jsonStream);
        }

        $this->handleErrorResponse($response, $jsonStream);
    }

    /**
     * Checks if the HTTP status code is successful.
     */
    public function isSuccessful(Response $response): bool
    {
        return $response->getStatusCode() < 400;
    }

    /**
     * Handles unsuccessful error codes by throwing the proper exception.
     * 
     * @throws ApiException
     * @throws UnauthorizedHttpException
     * @throws AccessDeniedHttpException
     * @throws NotFoundHttpException
     * @throws UnsupportedMediaTypeHttpException
     */
    public function handleErrorResponse(Response $response, StreamInterface $stream): void
    {
        switch ($response->getStatusCode()) {
            case 400:
                throw new ApiException($stream);
            case 401:
                throw new UnauthorizedHttpException;
            case 403:
                throw new AccessDeniedHttpException;
            case 404:
                throw new NotFoundHttpException;
            case 415:
                throw new UnsupportedMediaTypeHttpException;
        }
    }
}
