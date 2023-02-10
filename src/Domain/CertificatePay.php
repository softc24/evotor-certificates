<?php

namespace SoftC\Evotor\Certificates\Api\Domain;

use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;

// Сертификат (гашение)
class CertificatePay implements ArrayableInterface {
    // ИД сертификата
    public string $uuid;
    // Номинал для сертификатов по свободной цене
    public float $used;

    public function __construct(string $uuid, float $used) {
        $this->uuid = $uuid;
        $this->used = $used;
    }

    public function ToArray(): array {
        return [
            'uuid' => $this->uuid,
            'used' => $this->used,
        ];
    }
}