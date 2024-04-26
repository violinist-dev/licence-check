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

    public function __construct(int $expiry)
    {
        $this->expiry = $expiry;
    }
}