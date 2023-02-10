<?php

namespace SoftC\Evotor\Certificates\Api;

use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SoftC\Evotor\Certificates\Api\Entities\Certificate;
use SoftC\Evotor\Certificates\Api\Entities\CertificateType;
use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;
use SoftC\Evotor\Certificates\Api\Params\SelectCertificatesParams;
use SoftC\Evotor\Certificates\Api\Requests\CertificatesActivateRequest;
use SoftC\Evotor\Certificates\Api\Requests\CertificatesCreateRequest;
use SoftC\Evotor\Certificates\Api\Requests\CertificatesPayRequest;

/**
 * Клиент API
 *
 * @author aleksandr
 */
class Client {
    const BASE_URI = 'https://certs.evotor.tech/api/3rdparty';

    /**
     * Ключ доступа
     * @var string
     */
    protected string $token;
    /**
     * HTTP-клиент
     * @var \Http\Client\HttpClient
     */
    protected HttpClient $client;
    /**
     * Фабрика запросов
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    protected RequestFactoryInterface $requestFactory;
    /**
     * Фабрика потоков
     * @var StreamFactoryInterface
     */
    protected StreamFactoryInterface $streamFactory;
    protected HydratorInterface $hydrator;

    /**
     * Ключ доступа к API
     * @param string $token Ключ доступа к API
     * @param HttpClient $client HTTP-клиент, если не передан, то будет создан со значениями по-умолчанию
     */
    public function __construct(string $token, ?HttpClient $client = null) {
        $this->token = $token;

        $this->client = $client ?? HttpClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $hydrator = new ObjectPropertyHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        $this->hydrator = $hydrator;
    }

    /**
     * Получить виды сертификатов
     * @return array<CertificateType>
     */
    public function SelectTypes(): array {
        $uri = '/type';

        $data = $this->sendRequest('GET', $uri);

        return array_map(fn($item) => $this->hydrator->hydrate((array) $item, new CertificateType()), $data);
    }

    /**
     * Получить сертификаты
     * @return array<Certificate>
     */
    public function SelectCertificates(?SelectCertificatesParams $params = null): array {
        $uri = '/certificate';
        $query = empty($params) ? null : $params->ToQuery();
        empty($query)
            or $query = '?' . $query;

        $data = $this->sendRequest('GET', $uri . $query);

        return array_map(fn($item) => $this->hydrator->hydrate((array) $item, new Certificate()), $data);
    }

    /**
     * Получить сертификат по номеру
     * @param string $number
     * @return Certificate
     */
    public function GetCertificateByNumber(string $number): Certificate {
        $uri = '/certificate/' . urlencode($number);

        $data = $this->sendRequest('GET', $uri);

        return $this->hydrator->hydrate($data, new Certificate());
    }

    /**
     * Создать сертификаты
     * @return array<string>
     */
    public function CreateCertificates(CertificatesCreateRequest $request): array {
        if (empty($request->certificates)) {
            return [];
        }

        $uri = '/certificate';

        /** @var array<string> */
        $data = $this->sendRequest('POST', $uri, $request);

        return $data;
    }

    public function ActivateCertificates(CertificatesActivateRequest $request): void {
        if (empty($request->certificates)) {
            return;
        }

        $uri = '/certificate/activate';
        $this->sendRequest('POST', $uri, $request);
    }

    public function PayCertificates(CertificatesPayRequest $request): void {
        if (empty($request->certificates)) {
            return;
        }

        $uri = '/certificate/pay';
        $this->sendRequest('POST', $uri, $request);
    }

    /**
     * Выполняет запрос к серверу
     * @param string $method http-метод
     * @param string $uri адрес
     * @param ArrayableInterface|array<ArrayableInterface>|null $payload тело запроса
     * @throws HttpException
     * @throws \RuntimeException
     * @return array<mixed>
     */
    protected function sendRequest(string $method, string $uri, $payload = null) {
        $data = isset($payload)
            ? json_encode(
                is_array($payload)
                ? array_map(static fn(ArrayableInterface $item) => $item->ToArray(), $payload)
                : $payload->ToArray()
            )
            : null;
        if ($data === false) {
            throw new \RuntimeException('Не удалось сериализовать данные');
        }

        $request = $this->requestFactory
            ->createRequest(
                $method,
                    static::BASE_URI . $uri
            )
            ->withAddedHeader('Authorization', $this->token);
        if (isset($data)) {
            $request = $request
                ->withAddedHeader('Content-Type', 'application/json')
                ->withBody($this->streamFactory->createStream($data));
        }

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() >= 400) {
            throw HttpException::create($request, $response);
        }

        $result = json_decode($response->getBody(), true);
        if (!is_array($result)) {
            throw new \RuntimeException('Не удалось десериализовать ответ');
        }

        return $result;
    }
}