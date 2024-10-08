<?php

namespace violinist\LicenceCheck\Tests;

use Elliptic\EdDSA;
use PHPUnit\Framework\TestCase;
use violinist\LicenceCheck\Licence;
use violinist\LicenceCheck\LicenceChecker;
use violinist\LicenceCheck\LicenceGenerator;

class GeneratorTest extends TestCase
{
    public function testGenerator()
    {
        $new_private_key = bin2hex('real super secret yeah');
        $creator = new LicenceGenerator($new_private_key);
        $expriy = time() + 3600;
        $licence = new Licence($expriy, ['some' => 'data']);
        $licence_key = $creator->generateLicenceKey($licence);
        // Let's also check its validity, yeah?
        $ec = new EdDSA('ed25519');
        $key = $ec->keyFromSecret($new_private_key);
        $checker = LicenceChecker::createFromLicenceAndKey($licence_key, $key->getPublic('hex'));
        self::assertTrue($checker->isValid());
        self::assertEquals($expriy, $checker->getPayload()->getExpiry());
        self::assertEquals(['some' => 'data'], $checker->getPayload()->getData());
    }
}
