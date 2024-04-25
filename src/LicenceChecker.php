<?php

namespace violinist\LicenceCheck;

use Base64Url\Base64Url;
use Elliptic\EdDSA;
use MessagePack\BufferUnpacker;

class LicenceChecker
{
    public function __construct(string $public_key)
    {
        $this->publicKey = $public_key;
    }

    public function checkLicence(string $licence_key): bool
    {
        $decoded = Base64Url::decode($licence_key);
        $unzipped = \gzinflate($decoded);
        $unpacker = new BufferUnpacker($unzipped);
        $data = $unpacker->unpackArray();
        [$payload, $signature] = $data;
        $ec = new EdDSA('ed25519');
        $key = $ec->keyFromPublic($this->publicKey);
        return $key->verify($payload, $signature);
    }

}