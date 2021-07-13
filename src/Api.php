<?php
namespace Tustin\PlayStation;

use GuzzleHttp\Client;

use Tustin\Haste\Http\HttpClient;
use Tustin\PlayStation\Exception\UnmappedGraphQLOperationException;

class Api extends HttpClient
{
    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }

    public function graphql(string $op, array $variables)
    {
        // @Temp: This will hopefully be removed at some point for dynamic operation hashing.
        $hashMap = [
            'metGetConceptByProductIdQuery' => '0a4c9f3693b3604df1c8341fdc3e481f42eeecf961a996baaa65e65a657a6433',
            'metGetConceptById' => 'cc90404ac049d935afbd9968aef523da2b6723abfb9d586e5f77ebf7c5289006',
            'metGetProductById' => 'a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a',
        ];

        if (!array_key_exists($op, $hashMap)) {
            throw new UnmappedGraphQLOperationException('The GraphQL operation ' . $op . ' is not mapped.');
        }

        return $this->get('graphql/v1/op', [
            'operationName' => $op,
            'variables' => json_encode($variables),
            'extensions' => json_encode([
                'persistedQuery' => [
                    'version' => 1,
                    'sha256Hash' => $hashMap[$op]
                ]
            ])
        ])->data;
    }
}

