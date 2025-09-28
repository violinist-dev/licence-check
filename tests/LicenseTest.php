<?php

namespace violinist\LicenseCheck\Tests;

use PHPUnit\Framework\TestCase;
use violinist\LicenseCheck\License;

class LicenseTest extends TestCase
{

   /**
    * Test expiry
    */
    public function testLicenseExpiry()
    {
        $expiry = time() + 3600;
        $license = new License($expiry);
        self::assertEquals($expiry, $license->getExpiry());
    }

   /**
    * Test getting the data.
    */
    public function testLicenseData()
    {
        $data = ['some' => 'data'];
        $license = new License(time(), $data);
        self::assertEquals($data, $license->getData());
    }

  /**
   * Test if the license is valid for a repository.
   *
   * @dataProvider repoProvider
   */
    public function testValidForRepo($url, $expected_result)
    {
        // Without a prefix, always valid.
        $license = new License(time());
        self::assertTrue($license->isValidForRepository($url));
        $license = new License(time(), ['prefix' => 'https://github.com']);
        self::assertEquals($expected_result, $license->isValidForRepository($url));
    }

    public function repoProvider()
    {
        return [
            [
              'https://gitlab.com',
              false,
            ],
            [
              'https://github.com/user/repo',
              true,
            ],
            [
                'http://github.com/user/repo',
                true,
            ],
            [
                'git://github.com/user/repo',
                true,
            ],
            [
                'github.com/user/repo',
                true,
            ],
            [
                'www.github.com/user/repo',
                false,
            ],
            [
                'https://www.github.com/user/repo',
                false,
            ],
        ];
    }
}
