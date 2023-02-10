<?php

namespace SoftC\Evotor\Certificates\Api\Domain;

use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;

// Сертификат (запись)
class CertificateCreate implements ArrayableInterface {
    // Номер
    public string $number;
    // ИД вида сертификата
    public string $typeUuid;
    // Номинал для сертификатов по свободной цене
    public ?float $value;


    public function __construct(string $number, string $typeUuid, ?float $value = null) {
        $this->number = $number;
        $this->typeUuid = $typeUuid;
        $this->value = $value;
    }

    public function ToArray(): array {
        return [
            'number' => $this->number,
            'type_uuid' => $this->typeUuid,
            'value' => $this->value,
        ];
    }
}