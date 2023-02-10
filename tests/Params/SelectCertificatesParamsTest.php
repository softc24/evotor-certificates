<?php

namespace SoftC\Evotor\Certificates\Api\Tests\Params;

use PHPUnit\Framework\TestCase;
use SoftC\Evotor\Certificates\Api\Params\BaseParams;
use SoftC\Evotor\Certificates\Api\Params\SelectCertificatesParams;

final class SelectCertificatesParamsTest extends TestCase {
    public function testEmpty(): void {
        $params = new SelectCertificatesParams();

        $query = $params->ToQuery();

        $this->assertEquals('', $query);
    }

    public function testFrom(): void {
        $time = time();
        $params = new SelectCertificatesParams(
            \DateTimeImmutable::createFromFormat('U', '' . $time) ?: null
        );

        $query = $params->ToQuery();

        $this->assertEquals('updatedFrom=' . $time, $query);
    }

    public function testTo(): void {
        $time = time();
        $params = new SelectCertificatesParams(
            null,
            \DateTimeImmutable::createFromFormat('U', '' . $time) ?: null
        );

        $query = $params->ToQuery();

        $this->assertEquals('updatedTo=' . $time, $query);
    }

    public function testFromAndTo(): void {
        $time = time();
        $params = new SelectCertificatesParams(
            \DateTimeImmutable::createFromFormat('U', '' . $time) ?: null,
            \DateTimeImmutable::createFromFormat('U', '' . ($time + 1)) ?: null
        );

        $query = $params->ToQuery();

        $this->assertEquals(sprintf('updatedFrom=%d&updatedTo=%d', $time, $time + 1), $query);
    }
}