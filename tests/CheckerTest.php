<?php

namespace violinist\LicenceCheck\Tests;

use PHPUnit\Framework\TestCase;
use violinist\LicenceCheck\LicenceChecker;

class CheckerTest extends TestCase
{
    const PUBLIC_KEY = '2e71c0e69bc7c20603e5e8abc5ccef0d9a90fea7bfa4bb6fa2a86880e25e249d';

    public function testValid()
    {
        // IF you are thinking of copying this licence key, since that seems
        // convenient, then that will not work very well. First of all, it's
        // signed with a private key temporarily generated for this test.
        // Second, it was generated with an expiry date in the past. So while
        // the licence itself is valid, the expiry date is not. So many ways
        // this will not work.
        $generated_licence = 'fYtLakIxFEBdy1vB_SU3iaPrTRwVugFnj9AGxRYVsRSha-ju3m7qpFNHhwPn_C5vS38tDGW6jo_DOI7zZfcy5n6cu7_3ef8vU8HyfS6cyrR6Xq767XOcvqb1KKgoCKqo6_vyI02pWk6YgyU3gsrqgaS5pwcVo9aNY2AQbS1TZABJjwWRHCUqNrCK7pTd2TE6hA01rMQKTJUNmjlLjbYlYc4c3TQxS6iqYH8';
        $checker = LicenceChecker::createFromLicenceAndKey($generated_licence, self::PUBLIC_KEY);
        self::assertEquals(true, $checker->isValid());
    }

    public function testInvalid()
    {
        // This key is signed with a different private key. It's valid with the
        // public key of that one, but not with this one right here. Still,
        // should you verify it with the correct public key, the expiry will
        // still be in the past.
        $generated_licence = 'fY1BSgQxEEXnLH2CSlWSqmRWqaSyEryAuyZoUGbEEZlBBM_g7fo29satmw8PHu__bI_buM8EefmY55d5mpf3h7u5jtM66tNYn_9gyS5_XjJJXg7_m4dxfZ1vt-U4s2PnHXCidPzavgMDuKb7Ri7ShayAJ-oQLHiGohiiihfj0lBdjREF2NdYsUYkaYkRtfXmDZIESxiIBCqUaiq9MpYS9oMmqKrmejHd85ULUDGwXw';
        $checker = LicenceChecker::createFromLicenceAndKey($generated_licence, self::PUBLIC_KEY);
        self::assertEquals(false, $checker->isValid());
        self::assertEquals(LicenceChecker::INVALID_SIGNATURE, $checker->getErrorMessage());
    }
}