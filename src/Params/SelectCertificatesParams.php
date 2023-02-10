<?php

namespace SoftC\Evotor\Certificates\Api\Params;

class SelectCertificatesParams extends BaseParams {
    public ?int $updatedFrom;
    public ?int $updatedTo;

    public function __construct(?\DateTimeImmutable $updatedFrom = null, ?\DateTimeImmutable $updatedTo = null) {
        $this->updatedFrom = empty($updatedFrom)
            ? null
            : (int) $updatedFrom->format('U');
        $this->updatedTo = empty($updatedTo)
            ? null
            : (int) $updatedTo->format('U');
    }
}