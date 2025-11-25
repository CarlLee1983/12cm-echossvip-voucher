<?php

namespace Tests\Infrastructure\Http;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\RewardsCardGateway;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * RewardsCardGateway 單元測試。
 */
class RewardsCardGatewayTest extends TestCase
{
    protected ClientInterface $client;
    protected ApiContext $context;
    protected RewardsCardGateway $gateway;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->context = new ApiContext();
        $this->context->setToken('test-rewards-token');

        $this->gateway = new RewardsCardGateway($this->client, $this->context);
    }

    /**
     * 測試 post 方法成功回傳資料。
     */
    public function testPostReturnsDataOnSuccess(): void
    {
        $path = '/api/pos/test';
        $payload = ['data' => [['phone_number' => '0912345678']]];
        $responseBody = json_encode(['data' => ['message' => 'success', 'point' => 100]]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://stagevip-api.12cm.com.tw/api/pos/test',
                [
                    'headers' => ['Authorization' => 'Bearer test-rewards-token'],
                    'json' => $payload,
                ]
            )
            ->willReturn(new Response(200, [], $responseBody));

        $result = $this->gateway->post($path, $payload);

        $this->assertEquals(['message' => 'success', 'point' => 100], $result);
    }

    /**
     * 測試使用自訂 rewards card host。
     */
    public function testPostUsesCustomRewardsCardHost(): void
    {
        $context = new ApiContext(
            false,
            'https://prod.example.com',
            'https://sandbox.example.com',
            'https://custom-rewards.example.com'
        );
        $context->setToken('custom-token');
        $gateway = new RewardsCardGateway($this->client, $context);

        $responseBody = json_encode(['data' => []]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://custom-rewards.example.com/api/test',
                $this->anything()
            )
            ->willReturn(new Response(200, [], $responseBody));

        $gateway->post('/api/test', []);
    }

    /**
     * 測試回應中沒有 data 時回傳空陣列。
     */
    public function testPostReturnsEmptyArrayWhenNoData(): void
    {
        $responseBody = json_encode(['status' => 'ok']);

        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], $responseBody));

        $result = $this->gateway->post('/path', []);

        $this->assertEquals([], $result);
    }

    /**
     * 測試 HTTP 錯誤拋出 ResponseTypeException。
     */
    public function testPostThrowsResponseTypeExceptionOnHttpError(): void
    {
        $request = new Request('POST', '/test');
        $response = new Response(422, [], '{"message":"phone number is not exist."}');
        $exception = new RequestException('Error', $request, $response);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('{"message":"phone number is not exist."}');

        $this->gateway->post('/test', []);
    }

    /**
     * 測試 401 未授權錯誤。
     */
    public function testPostThrowsResponseTypeExceptionOn401(): void
    {
        $request = new Request('POST', '/test');
        $response = new Response(401, [], '{"message":"Invalid token"}');
        $exception = new RequestException('Unauthorized', $request, $response);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode(401);

        $this->gateway->post('/test', []);
    }

    /**
     * 測試連線錯誤拋出 RequestTypeException。
     */
    public function testPostThrowsRequestTypeExceptionOnConnectionError(): void
    {
        $request = new Request('POST', '/test');
        $exception = new RequestException('Network unreachable', $request);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('Request Error.');

        $this->gateway->post('/test', []);
    }

    /**
     * 測試正確傳送 Authorization header。
     */
    public function testPostSendsCorrectAuthorizationHeader(): void
    {
        $this->context->setToken('my-rewards-token');
        $responseBody = json_encode(['data' => []]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->callback(function ($options) {
                    return isset($options['headers']['Authorization'])
                        && $options['headers']['Authorization'] === 'Bearer my-rewards-token';
                })
            )
            ->willReturn(new Response(200, [], $responseBody));

        $this->gateway->post('/path', []);
    }

    /**
     * 測試 accumulate point 路徑。
     */
    public function testPostWithAccumulatePointPath(): void
    {
        $payload = ['data' => [['phone_number' => '0912345678', 'amount' => 100]]];
        $responseBody = json_encode(['data' => ['point' => 10, 'amount' => 100]]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://stagevip-api.12cm.com.tw/api/pos/mps-card-send-point',
                $this->anything()
            )
            ->willReturn(new Response(200, [], $responseBody));

        $result = $this->gateway->post('/api/pos/mps-card-send-point', $payload);

        $this->assertEquals(['point' => 10, 'amount' => 100], $result);
    }

    /**
     * 測試 deplete point 路徑。
     */
    public function testPostWithDepletePointPath(): void
    {
        $payload = ['data' => [['phone_number' => '0912345678', 'point' => 5]]];
        $responseBody = json_encode(['data' => ['message' => 'Points deducted']]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://stagevip-api.12cm.com.tw/api/pos/mps-card-deduct-point',
                $this->anything()
            )
            ->willReturn(new Response(200, [], $responseBody));

        $result = $this->gateway->post('/api/pos/mps-card-deduct-point', $payload);

        $this->assertEquals(['message' => 'Points deducted'], $result);
    }

    /**
     * 測試空 payload。
     */
    public function testPostWithEmptyPayload(): void
    {
        $responseBody = json_encode(['data' => ['result' => 'empty']]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->callback(function ($options) {
                    return $options['json'] === [];
                })
            )
            ->willReturn(new Response(200, [], $responseBody));

        $result = $this->gateway->post('/path', []);

        $this->assertEquals(['result' => 'empty'], $result);
    }

    /**
     * 測試 500 伺服器錯誤。
     */
    public function testPostThrowsResponseTypeExceptionOn500(): void
    {
        $request = new Request('POST', '/test');
        $response = new Response(500, [], '{"message":"Internal Server Error"}');
        $exception = new RequestException('Server Error', $request, $response);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode(500);

        $this->gateway->post('/test', []);
    }
}

