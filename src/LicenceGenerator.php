<?php

namespace violinist\LicenceCheck;

use Base64Url\Base64Url;
use Elliptic\EdDSA;
use MessagePack\Packer;

class LicenceGenerator
{
    private $privateKey;

    public function __construct(string $private_key)
    {
        $this->privateKey = $private_key;
    }

    public function generateLicenceKey(Licence $licence): string
    {
        $ec = new EdDSA('ed25519');
        $key = $ec->keyFromSecret($this->privateKey);

        $packer = new Packer();
        $body = $packer->pack(serialize($licence));

        $signature = $key->sign(bin2hex($body));

        $data = [$body, $signature->toHex()];
        $body_and_signature = $packer->pack($data);
        $compressed = gzdeflate($body_and_signature, 9);
        return Base64Url::encode($compressed);
    }
}
