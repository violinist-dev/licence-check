<?php

namespace violinist\LicenceCheck;

final class Licence
{

    /**
     * The expiry date of the licence in a timestamp.
     *
     * @var int
     */
    private $expiry;

    private $data = [];

    public function __construct(int $expiry, $data = [])
    {
        $this->expiry = $expiry;
        $this->data = $data;
    }

    public function getExpiry(): int
    {
        return $this->expiry;
    }

    public function getData()
    {
        return $this->data;
    }
}