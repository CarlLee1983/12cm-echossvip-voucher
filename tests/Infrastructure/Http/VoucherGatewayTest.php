<?php

namespace Tests\Infrastructure\Http;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\VoucherGateway;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * VoucherGateway 單元測試。
 */
class VoucherGatewayTest extends TestCase
{
    protected ClientInterface $client;
    protected ApiContext $context;
    protected VoucherGateway $gateway;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->context = new ApiContext(true); // sandbox mode
        $this->context->setToken('test-token');

        $this->gateway = new VoucherGateway($this->client, $this->context);
    }

    /**
     * 測試 post 方法成功回傳資料。
     */
    public function testPostReturnsDataOnSuccess(): void
    {
        $path = '/test/path';
        $payload = ['key' => 'value'];
        $responseBody = json_encode(['data' => ['result' => 'success']]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://testservice.12cm.com.tw/test/path',
                [
                    'headers' => ['Authorization' => 'Bearer test-token'],
                    'json' => $payload,
                ]
            )
            ->willReturn(new Response(200, [], $responseBody));

        $result = $this->gateway->post($path, $payload);

        $this->assertEquals(['result' => 'success'], $result);
    }

    /**
     * 測試 post 方法使用 production 環境。
     */
    public function testPostUsesProductionBaseUri(): void
    {
        $context = new ApiContext(false); // production mode
        $context->setToken('prod-token');
        $gateway = new VoucherGateway($this->client, $context);

        $responseBody = json_encode(['data' => []]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://service.12cm.com.tw/api/test',
                $this->callback(function ($options) {
                    return $options['headers']['Authorization'] === 'Bearer prod-token';
                })
            )
            ->willReturn(new Response(200, [], $responseBody));

        $gateway->post('/api/test', []);
    }

    /**
     * 測試回應中沒有 data 時回傳空陣列。
     */
    public function testPostReturnsEmptyArrayWhenNoData(): void
    {
        $responseBody = json_encode(['message' => 'no data field']);

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
        $response = new Response(422, [], '{"message":"Validation Error"}');
        $exception = new RequestException('Error', $request, $response);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('{"message":"Validation Error"}');

        $this->gateway->post('/test', []);
    }

    /**
     * 測試 401 未授權錯誤。
     */
    public function testPostThrowsResponseTypeExceptionOn401(): void
    {
        $request = new Request('POST', '/test');
        $response = new Response(401, [], '{"message":"Unauthorized"}');
        $exception = new RequestException('Unauthorized', $request, $response);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode(401);

        $this->gateway->post('/test', []);
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

    /**
     * 測試連線錯誤（無回應）拋出 RequestTypeException。
     */
    public function testPostThrowsRequestTypeExceptionOnConnectionError(): void
    {
        $request = new Request('POST', '/test');
        $exception = new RequestException('Connection failed', $request);

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
        $this->context->setToken('my-secret-token');
        $responseBody = json_encode(['data' => []]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->callback(function ($options) {
                    return isset($options['headers']['Authorization'])
                        && $options['headers']['Authorization'] === 'Bearer my-secret-token';
                })
            )
            ->willReturn(new Response(200, [], $responseBody));

        $this->gateway->post('/path', []);
    }

    /**
     * 測試正確傳送 JSON payload。
     */
    public function testPostSendsJsonPayload(): void
    {
        $payload = ['phone_number' => '0912345678', 'amount' => 100];
        $responseBody = json_encode(['data' => ['success' => true]]);

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->callback(function ($options) use ($payload) {
                    return isset($options['json']) && $options['json'] === $payload;
                })
            )
            ->willReturn(new Response(200, [], $responseBody));

        $this->gateway->post('/path', $payload);
    }

    /**
     * 測試空 payload 的處理。
     */
    public function testPostWithEmptyPayload(): void
    {
        $responseBody = json_encode(['data' => ['result' => 'ok']]);

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

        $this->assertEquals(['result' => 'ok'], $result);
    }
}

