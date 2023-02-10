<?php

namespace SoftC\Evotor\Certificates\Api\Domain;

use SoftC\Evotor\Certificates\Api\Interfaces\ArrayableInterface;

// Сертификат (активация)
class CertificateActivate implements ArrayableInterface {
    // ИД сертификата
    public string $uuid;
    // ИД вида сертификата
    public string $typeUuid;
    // Номинал для сертификатов по свободной цене
    public ?float $value;
    // Действует до
    public ?int $validityPeriod;

    public function __construct(string $uuid, string $typeUuid, ?int $validityPeriod = null, ?float $value = null) {
        $this->uuid = $uuid;
        $this->typeUuid = $typeUuid;
        $this->value = $value;
        $this->validityPeriod = $validityPeriod;
    }

    public function setValidityDate(?\DateTimeImmutable $validityDate): void {
        $this->validityPeriod = empty($validityDate)
            ? null
            : (int) $validityDate->format('U');
    }

    public function ToArray(): array {
        return [
            'uuid' => $this->uuid,
            'type_uuid' => $this->typeUuid,
            'value' => $this->value,
            'validity_period' => $this->validityPeriod,
        ];
    }
}