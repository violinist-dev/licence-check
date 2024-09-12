<?php

namespace violinist\LicenceCheck;

final class Licence
{

    const PREFIX_DATA_KEY = 'prefix';

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

    public function isValidForRepository(string $url) : bool
    {
        // First check if we even have any data about a required prefix.
        if (empty($this->data[self::PREFIX_DATA_KEY])) {
            return true;
        }
        // Then check if the URL starts with the required prefix.
        return strpos($url, $this->data[self::PREFIX_DATA_KEY]) === 0;
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