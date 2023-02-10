<?php

namespace SoftC\Evotor\Certificates\Api\Tests\Params;

use PHPUnit\Framework\TestCase;
use SoftC\Evotor\Certificates\Api\Params\BaseParams;

final class BaseParamsTest extends TestCase {
    public function testToQuery(): void {
        $item = new Tmp('123', 456);
        $query = $item->ToQuery();

        $this->assertEquals("a=123&b=456", $query);
    }
}

class Tmp extends BaseParams {
    public string $a;
    public int $b;

    public function __construct(string $a, int $b) {
        $this->a = $a;
        $this->b = $b;
    }
}