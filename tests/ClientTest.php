<?php

namespace SoftC\Evotor\Certificates\Api\Tests;

use Http\Client\Curl\Client as CurlClient;
use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SoftC\Evotor\Certificates\Api\Client;
use Http\Mock\Client as MockClient;
use SoftC\Evotor\Certificates\Api\Domain\CertificateActivate;
use SoftC\Evotor\Certificates\Api\Domain\CertificateCreate;
use SoftC\Evotor\Certificates\Api\Domain\CertificatePay;
use SoftC\Evotor\Certificates\Api\Entities\Certificate;
use SoftC\Evotor\Certificates\Api\Entities\CertificateType;
use SoftC\Evotor\Certificates\Api\Enums\CertificateStatus;
use SoftC\Evotor\Certificates\Api\Enums\EntityStatus;
use SoftC\Evotor\Certificates\Api\Params\SelectCertificatesParams;
use SoftC\Evotor\Certificates\Api\Requests\CertificatesActivateRequest;
use SoftC\Evotor\Certificates\Api\Requests\CertificatesCreateRequest;
use SoftC\Evotor\Certificates\Api\Requests\CertificatesPayRequest;

final class ClientTest extends TestCase {
    private static ResponseFactoryInterface $responseFactory;
    private static StreamFactoryInterface $streamFactory;

    private Client $client;
    private MockClient $mockClient;

    public static function setUpBeforeClass(): void {
        self::$responseFactory = Psr17FactoryDiscovery::findResponseFactory();
        self::$streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    protected function setUp(): void {
        $this->mockClient = new MockClient(self::$responseFactory);
        $this->client = new Client(self::TOKEN, $this->mockClient);
    }

    public function testHeaders(): void {
        $this->mockClient->addResponse(
            $this->mockResponse('[]')
        );

        $this->client->SelectTypes();

        $req = $this->mockClient->getLastRequest();
        $this->assertEquals(self::TOKEN, $req->getHeaderLine('Authorization'));
    }

    public function testSelectTypes(): void {
        $this->mockClient->addResponse(
            $this->mockResponse('[{
                "uuid": "54931127-e63a-4b17-8acc-60e56af2bce9",
                "status": "active",
                "name": "Сертификат 2000 руб.",
                "value": "2000.00",
                "number_prefix": null,
                "number_length": null,
                "number_start": null,
                "number_end": null,
                "validity_days": null,
                "products": {
                  "20190326-56C4-4098-8060-039C6BF50FC7": "996008a5-506d-4838-ad3e-0568ef0dd1c4"
                }
              }]')
        );

        $data = $this->client->SelectTypes();

        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('/api/3rdparty/type', $req->getUri()->getPath());

        $this->assertCount(1, $data);
        $this->assertInstanceOf(CertificateType::class, $data[0]);
        $this->assertEquals('54931127-e63a-4b17-8acc-60e56af2bce9', $data[0]->uuid);
    }

    public function testSelectCertificates(): void {
        $time = time();
        $this->mockClient->addResponse(
            $this->mockResponse('[{
                  "uuid": "ed9728a8-501d-44aa-8514-cb66d5d9895a",
                  "account_id": "83",
                  "type_uuid": "ddf201e6-6296-4974-8bb3-7f3e63cf8667",
                  "status": "new",
                  "number": "1",
                  "value": "1000.00",
                  "balance": "1000.00",
                  "validity_period": null,
                  "createdAt": "1675069449",
                  "createdIn_uuid": null,
                  "createdIn_name": null,
                  "createdBy": "01-000000000722610",
                  "activate_receipt": null,
                  "usedAt": null,
                  "usedIn_uuid": null,
                  "usedIn_name": null,
                  "usedBy": null,
                  "updatedAt": "1675069449",
                  "updatedOn_uuid": "Mozilla/5.0 (Windows NT 6.1; Win64;",
                  "pay_receipt": null
                }]')
        );

        $data = $this->client->SelectCertificates(
            new SelectCertificatesParams(
                \DateTimeImmutable::createFromFormat('U', (string) $time) ?: null
            )
        );

        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('/api/3rdparty/certificate', $req->getUri()->getPath());
        $this->assertEquals('updatedFrom=' . $time, $req->getUri()->getQuery());

        $this->assertCount(1, $data);
        $this->assertInstanceOf(Certificate::class, $data[0]);
        $this->assertEquals('ed9728a8-501d-44aa-8514-cb66d5d9895a', $data[0]->uuid);
        $this->assertEquals('ddf201e6-6296-4974-8bb3-7f3e63cf8667', $data[0]->typeUuid);
    }

    public function testCreateCertificates(): void {
        $this->mockClient->addResponse(
            $this->mockResponse('["f8160d5a-7359-45db-9e55-755d36761128"]')
        );

        $request = new CertificatesCreateRequest([
            new CertificateCreate('123', '158cfc9f-dd09-4f33-b575-88d7de186adb'),
        ]);

        $data = $this->client->CreateCertificates($request);

        $req = $this->mockClient->getLastRequest();
        $this->assertEquals('/api/3rdparty/certificate', $req->getUri()->getPath());
        $this->assertEquals('POST', $req->getMethod());

        $payload = $req->getBody()->getContents();
        $this->assertJson($payload);
        $this->assertJsonStringEqualsJsonString(
            '{"certificates":[{"number":"123","type_uuid":"158cfc9f-dd09-4f33-b575-88d7de186adb","value":null}]}',
            $payload
        );

        $this->assertCount(1, $data);
        $this->assertContains('f8160d5a-7359-45db-9e55-755d36761128', $data);
    }

    public function testServer(): void {
        $token = getenv('TEST_TOKEN');
        if (empty($token)) {
            $this->markTestSkipped();
        }

        $curlClient = new CurlClient(null, null, [
            CURLOPT_TIMEOUT => 1,
        ]);
        $client = new Client($token, $curlClient);

        // select types
        $types = $client->SelectTypes();
        $types = array_filter($types, static fn(CertificateType $item) => $item->status === EntityStatus::ACTIVE && $item->value > 0);
        $this->assertNotEmpty($types);

        $type = $types[0];

        // create certificate
        $number = bin2hex(random_bytes(16));
        $certs = $client->CreateCertificates(new CertificatesCreateRequest([
            new CertificateCreate($number, $type->uuid)
        ]));
        $this->assertCount(1, $certs);

        $cert = $client->GetCertificateByNumber($number);
        $this->assertEquals(CertificateStatus::NEW , $cert->status);

        // activate certificate
        $uuid = $certs[0];
        $client->ActivateCertificates(new CertificatesActivateRequest(
            bin2hex(random_bytes(18)),
            [
                new CertificateActivate($uuid, $type->uuid)
            ]
        ));

        $cert = $client->GetCertificateByNumber($number);
        $this->assertEquals(CertificateStatus::ACTIVE, $cert->status);

        // pay certificate
        $client->PayCertificates(new CertificatesPayRequest(
            bin2hex(random_bytes(18)),
            [
                new CertificatePay($uuid, $type->value ?? 0)
            ]
        ));

        $cert = $client->GetCertificateByNumber($number);
        $this->assertEquals(CertificateStatus::USED, $cert->status);
    }

    /**
     * Создает фальшивый ответ
     * @param string $body тело ответа
     * @param int $code код ответа
     * @param array<string, string> $headers заголовки ответа
     * @return ResponseInterface
     */
    private function mockResponse(string $body, int $code = 200, array $headers = ['Content-Type' => 'application/json']): ResponseInterface {
        $response = self::$responseFactory
            ->createResponse($code)
            ->withBody(self::$streamFactory->createStream($body));

        foreach ($headers as $key => $value) {
            $response = $response->withAddedHeader(
                $key,
                $value
            );
        }

        return $response;
    }

    const TOKEN = '83f4ceb7-6754-4b3f-a613-216040b865df';
}