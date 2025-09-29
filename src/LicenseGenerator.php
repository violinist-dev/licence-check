<?php

namespace violinist\LicenseCheck;

use Base64Url\Base64Url;
use Elliptic\EdDSA;
use MessagePack\Packer;

class LicenseGenerator
{
    private $privateKey;

    public function __construct(string $private_key)
    {
        $this->privateKey = $private_key;
    }

    public function generateLicenseKey(License $license): string
    {
        $ec = new EdDSA('ed25519');
        $key = $ec->keyFromSecret($this->privateKey);

        $packer = new Packer();
        $body = $packer->pack(serialize($license));

        $signature = $key->sign(bin2hex($body));

        $data = [$body, $signature->toHex()];
        $body_and_signature = $packer->pack($data);
        $compressed = gzdeflate($body_and_signature, 9);
        return Base64Url::encode($compressed);
    }
}
