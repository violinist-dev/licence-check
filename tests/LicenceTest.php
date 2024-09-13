<?php

namespace violinist\LicenceCheck\Tests;

use PHPUnit\Framework\TestCase;
use violinist\LicenceCheck\Licence;

class LicenceTest extends TestCase
{

  /**
   * Test expiry
   */
    public function testLicenceExpiry()
    {
        $expiry = time() + 3600;
        $licence = new Licence($expiry);
        self::assertEquals($expiry, $licence->getExpiry());
    }

  /**
   * Test getting the data.
   */
    public function testLicenceData()
    {
        $data = ['some' => 'data'];
        $licence = new Licence(time(), $data);
        self::assertEquals($data, $licence->getData());
    }
}
