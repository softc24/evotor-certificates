<?php

namespace SoftC\Evotor\Certificates\Api\Requests;

use SoftC\Evotor\Certificates\Api\Domain\CertificateActivate;
use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;

final class CertificatesActivateRequest implements ArrayableInterface {
    public string $receiptUuid;
    /**
     * Список сертификатов
     * @var array<CertificateActivate>
     */
    public array $certificates;

    /**
     * @param string $operationId ИД операции
     * @param array<CertificateActivate> $certificates список сертификатов
     */
    public function __construct(string $operationId, array $certificates) {
        $this->receiptUuid = $operationId;
        $this->certificates = $certificates;
    }

    public function ToArray(): array {
        return [
            'receipt_uuid' => $this->receiptUuid,
            'certificates' => array_map(static fn(CertificateActivate $item) => $item->ToArray(), $this->certificates),
        ];
    }
}