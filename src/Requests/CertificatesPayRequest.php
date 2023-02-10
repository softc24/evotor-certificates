<?php

namespace SoftC\Evotor\Certificates\Api\Requests;

use SoftC\Evotor\Certificates\Api\Domain\CertificatePay;
use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;

final class CertificatesPayRequest implements ArrayableInterface {
    public string $receiptUuid;
    /**
     * Список сертификатов
     * @var array<CertificatePay>
     */
    public array $certificates;

    /**
     * @param string $operationId ИД операции
     * @param array<CertificatePay> $certificates список сертификатов
     */
    public function __construct(string $operationId, array $certificates) {
        $this->receiptUuid = $operationId;
        $this->certificates = $certificates;
    }

    public function ToArray(): array {
        return [
            'receipt_uuid' => $this->receiptUuid,
            'certificates' => array_map(static fn(CertificatePay $item) => $item->ToArray(), $this->certificates),
        ];
    }
}