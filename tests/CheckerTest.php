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

    public function testVeryInvalid()
    {
        // This key is just gibberish.
        $generated_licence = 'this is not a valid licence key';
        $checker = LicenceChecker::createFromLicenceAndKey($generated_licence, self::PUBLIC_KEY);
        self::assertEquals(false, $checker->isValid());
    }

    public function testInvalidLength()
    {
        // This key contains 3 items in the array. It's also of course not valid
        // based on the date, but that's another story.
        $generated_licence = 'hY1BTsMwFER7FOQT-Ps7tuOs_J3_V0hcgE2UWsIC2qqpqlaoEnfgFghxn9yGFMQWNqMZzRvN2_w5f9xF1FEd6_apbup0uL-tY9mMJT-U8fE3qGjiyxQxRLX6m1yV067uz6qr0aIxwYPT3bJ0_y_Xw2FQ3RDhemWjmrbPRXXf9qe6XOZXhxkQhAITkQ4pOPKSUrtkEfS9ZtBCDOxBvJXEhpJtW_ZZPFJYYEqNNAYWRAsn4gAAPYckYgikEYuS-za7hr0DYYNBjM5gNLhe5_d12e_ON1f9Ag';
        $checker = LicenceChecker::createFromLicenceAndKey($generated_licence, self::PUBLIC_KEY);
        self::assertEquals(false, $checker->isValid());
        self::assertEquals(LicenceChecker::WRONG_DATA_PARTS, $checker->getErrorMessage());
    }
}
