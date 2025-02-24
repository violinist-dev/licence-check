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

  /**
   * Test if the licence is valid for a repository.
   *
   * @dataProvider repoProvider
   */
    public function testValidForRepo($url, $expected_result)
    {
        // Without a prefix, always valid.
        $licence = new Licence(time());
        self::assertTrue($licence->isValidForRepository($url));
        $licence = new Licence(time(), ['prefix' => 'https://github.com']);
        self::assertEquals($expected_result, $licence->isValidForRepository($url));
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
        ];
    }
}
