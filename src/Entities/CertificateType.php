<?php

namespace SoftC\Evotor\Certificates\Api\Entities;

// Вид сертификата
class CertificateType extends Base {
    // Состояние
    public string $status;
    // Наименование
    public string $name;
    /**
     * Номинал
     * @var float|null
     */
    public $value;
    // Префикс номера
    public ?string $numberPrefix;
    // Длина номера
    public ?int $numberLength;
    // Начальный номер
    public ?int $numberStart;
    // Конечный номер
    public ?int $numberEnd;
    // Срок действия в днях
    public ?int $validityDays;
    /**
     * ИЛ номенклатуры в разрезе торговых точек
     * @var array<string,string>
     */
    public array $products;
}