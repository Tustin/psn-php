<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tustin\Haste\Http\JsonStream;
use Tustin\PlayStation\Api;
use PHPUnit\Framework\TestCase;
use Tustin\PlayStation\Exception\UnmappedGraphQLOperationException;

class ApiTest extends TestCase
{

    private Api $api;
    private $client;

    public function testItShouldGraphql(): void
    {
        foreach ($this->getHashMap() as $op => $hash) {
            $this->client = $this->createMock(Client::class);
            $this->api = new Api($this->client);

            $variables = [];
            $response = new Response(200, [], '{"data": "some-data-' . $op . '"}');

            $this->client
                ->expects($this->once())
                ->method('get')
                ->with('graphql/v1/op', [
                    'query' => [
                        'operationName' => $op,
                        'variables' => json_encode($variables),
                        'extensions' => json_encode([
                            'persistedQuery' => [
                                'version' => 1,
                                'sha256Hash' => $hash,
                            ],
                        ]),
                    ],
                    'headers' => [],
                ])
                ->willReturn($response->withBody(new JsonStream($response->getBody())));

            $this->assertEquals('some-data-' . $op, $this->api->graphql($op, $variables));
        }
    }

    public function testItShouldThrowWithInvalidOperation(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->api = new Api($this->client);

        $variables = [];
        $op = 'invalid-operation';

        $this->client
            ->expects($this->never())
            ->method('get');

        $this->expectException(UnmappedGraphQLOperationException::class);
        $this->expectExceptionMessage('The GraphQL operation ' . $op . ' is not mapped.');
        $this->api->graphql($op, $variables);
    }

    private function getHashMap(): array
    {
        return [
            'metGetConceptByProductIdQuery' => '0a4c9f3693b3604df1c8341fdc3e481f42eeecf961a996baaa65e65a657a6433',
            'metGetConceptById' => 'cc90404ac049d935afbd9968aef523da2b6723abfb9d586e5f77ebf7c5289006',
            'metGetProductById' => 'a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a',
            'metGetAddOnsByTitleId' => 'e98d01ff5c1854409a405a5f79b5a9bcd36a5c0679fb33f4e18113c157d4d916',
            'metGetCategoryGrid' => 'b67a9e4414b80d8d762bf12a588c6125467ae0bb3bbe3cee3f7696c6984f8ef6',
            'metGetCategoryGrids' => 'cc0b6513521c59a321bf62334fa23a92f22cd2ce1abe9f014fadac6379e414a8',
            'metGetCategoryStrands' => '55ab5f168bec56f8362b5519f59faaf786d4e1cfeabb8bc969d6a65545e14f4d',
            'metGetDefaultView' => 'bec1b8a3b0bae8c08e3ce2c7fe2f38a69343434ccfbcdd82cc1f2e44f86b7c40',
            'metGetPricingDataByConceptId' => 'abcb311ea830e679fe2b697a27f755764535d825b24510ab1239a4ca3092bd09',
            'metGetStoreWishlist' => '571149e8aa4d76af7dd33b92e1d6f8f828ebc5fa8f0f6bf51a8324a0e6d71324',
            'metGetViews' => '6fd98ff7fecb603006fb5d92db176d5028435be163c8d1ee9f7c598ab4677dd1',
            'metGetWebCheckoutCart' => '2d4165c4de76877a32f3d08c91ce2af0e01d69300131fed0a8022868235e85b1',
            'metGetExperience' => '054e61ee68bbeadc21435caebcc4f2bba0919a99b06629d141b0b82dc55f10c4',
        ];
    }


}
