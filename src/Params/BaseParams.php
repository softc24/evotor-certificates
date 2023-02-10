<?php

namespace SoftC\Evotor\Certificates\Api\Params;

abstract class BaseParams {
    public function ToQuery(): string {
        $params = $this->toArray();

        return http_build_query($params);
    }

    /**
     * Возвращает представление параметров в виде массива
     * @return array<string,mixed>
     */
    protected function toArray(): array {
        return get_object_vars($this);
    }
}