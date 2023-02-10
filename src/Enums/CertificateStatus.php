<?php

namespace SoftC\Evotor\Certificates\Api\Enums;

// Состояние сертификата
abstract class CertificateStatus {
    // новый - не продан
    const NEW = 'new';
    // подготовлен к продаже - добавлен в чек, но не оплачен
    const PREPARED = 'prepared';
    // активный
    const ACTIVE = 'active';
    // зарезервирован - добавлен в оплату, но не погашен
    const RESERVED = 'reserved';
    // погашен
    const USED = 'used';
    // возвращен
    const RETURNED = 'returned';
    // заблокирован
    const BLOCKED = 'blocked';
    // подготовлен к возврату
    const PRERETURN = 'prereturn';
}