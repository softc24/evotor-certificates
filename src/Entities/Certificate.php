<?php

namespace SoftC\Evotor\Certificates\Api\Entities;

// Сертификат
class Certificate extends Base {
    // Состояние
    public string $status;
    // ИД вида сертификата
    public string $typeUuid;
    // Номер
    public string $number;
    /**
     * Номинал
     * @var float
     */
    public $value;
    /**
     * Баланс
     * @var float
     */
    public $balance;
    /**
     * Действует до
     * @var int|null
     */
    public $validityPeriod;

    public function getValidityDate(): ?\DateTimeImmutable {
        return empty($this->validityPeriod)
            ? null
            : (\DateTimeImmutable::createFromFormat('U', (string) $this->validityPeriod) ?: null);
    }
}