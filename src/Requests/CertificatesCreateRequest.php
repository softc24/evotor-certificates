<?php

namespace SoftC\Evotor\Certificates\Api\Requests;

use SoftC\Evotor\Certificates\Api\Domain\CertificateCreate;
use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;

final class CertificatesCreateRequest implements ArrayableInterface {
    /**
     * Список сертификатов
     * @var array<CertificateCreate>
     */
    public array $certificates;

    /**
     * @param array<CertificateCreate> $certificates список сертификатов
     */
    public function __construct(array $certificates) {
        $this->certificates = $certificates;
    }

    public function ToArray(): array {
        return [
            'certificates' => array_map(static fn(CertificateCreate $item) => $item->ToArray(), $this->certificates),
        ];
    }
}