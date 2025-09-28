<?php

namespace violinist\LicenseCheck\Tests;

use Elliptic\EdDSA;
use PHPUnit\Framework\TestCase;
use violinist\LicenseCheck\License;
use violinist\LicenseCheck\LicenseChecker;
use violinist\LicenseCheck\LicenseGenerator;

class GeneratorTest extends TestCase
{
    public function testGenerator()
    {
        $new_private_key = bin2hex('real super secret yeah');
        $creator = new LicenseGenerator($new_private_key);
        $expiry = time() + 3600;
        $license = new License($expiry, ['some' => 'data']);
        $license_key = $creator->generateLicenseKey($license);
        // Let's also check its validity, yeah?
        $ec = new EdDSA('ed25519');
        $key = $ec->keyFromSecret($new_private_key);
        $checker = LicenseChecker::createFromLicenseAndKey($license_key, $key->getPublic('hex'));
        self::assertTrue($checker->isValid());
        self::assertEquals($expiry, $checker->getPayload()->getExpiry());
        self::assertEquals(['some' => 'data'], $checker->getPayload()->getData());
    }
}
