<?php

namespace SoftC\Evotor\Certificates\Api\Interfaces;

interface ArrayableInterface {
    /**
     * @return array<string, mixed>
     */
    public function ToArray(): array;
}