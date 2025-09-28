<?php

namespace violinist\LicenseCheck\Tests;

use PHPUnit\Framework\TestCase;
use violinist\LicenseCheck\LicenseChecker;

class CheckerTest extends TestCase
{
    const PUBLIC_KEY = '19dd3dce894e2845aebb85a56ca4fe620c01b0fae31054da4b0ff8e59c314d9e';

    public function testValid()
    {
        // IF you are thinking of copying this license key, since that seems
        // convenient, then that will not work very well. First of all, it's
        // signed with a private key temporarily generated for this test.
        // Second, it was generated with an expiry date in the past. So while
        // the license itself is valid, the expiry date is not. So many ways
        // this will not work.
        $generated_license = 'hY1NTsMwEEZ7Fh-AjifxeOysbE-yQuICbKJiVKt_UROhIFSJM3AhxDFyG8KCLSw_6b33fSxfy-eDr8Crl3I5lnMZp8f7ssvnMad93h1-h_Lo30ZfsVebv8lNnodyfVVN8do6IE3I1Kwq_a8-9VOvmt7rn6-VH675ucxqtbXzaj9Nw-i32zz3p-GY73aXk2put-U9WnRBnDERADpwjgNYaV0kzZ2w4yqRJaogphgwEpqWI3NqMQiiwaANCHHVdphCjFKzJJFUdyEYB3UtOqFtmSpGpBoCW0hrSgwH0RboGw';
        $checker = LicenseChecker::createFromLicenseAndKey($generated_license, self::PUBLIC_KEY);
        self::assertEquals(true, $checker->isValid());
    }

    public function testInvalid()
    {
        // This key is signed with a different private key. It's valid with the
        // public key of that one, but not with this one right here. Still,
        // should you verify it with the correct public key, the expiry will
        // still be in the past.
        $generated_license = 'fY1BSgQxEEXnLH2CSlWSqmRWqaSyEryAuyZoUGbEEZlBBM_g7fo29satmw8PHu__bI_buM8EefmY55d5mpf3h7u5jtM66tNYn_9gyS5_XjJJXg7_m4dxfZ1vt-U4s2PnHXCidPzavgMDuKb7Ri7ShayAJ-oQLHiGohiiihfj0lBdjREF2NdYsUYkaYkRtfXmDZIESxiIBCqUaiq9MpYS9oMmqKrmejHd85ULUDGwXw';
        $checker = LicenseChecker::createFromLicenseAndKey($generated_license, self::PUBLIC_KEY);
        self::assertEquals(false, $checker->isValid());
        self::assertEquals(LicenseChecker::INVALID_SIGNATURE, $checker->getErrorMessage());
    }

    public function testVeryInvalid()
    {
        // This key is just gibberish.
        $generated_license = 'this is not a valid license key';
        $checker = LicenseChecker::createFromLicenseAndKey($generated_license, self::PUBLIC_KEY);
        self::assertEquals(false, $checker->isValid());
    }

    public function testInvalidLength()
    {
        // This key contains 3 items in the array. It's also of course not valid
        // based on the date, but that's another story.
        $generated_license = 'hY1BTsMwFER7FOQT-Ps7tuOs_J3_V0hcgE2UWsIC2qqpqlaoEnfgFghxn9yGFMQWNqMZzRvN2_w5f9xF1FEd6_apbup0uL-tY9mMJT-U8fE3qGjiyxQxRLX6m1yV067uz6qr0aIxwYPT3bJ0_y_Xw2FQ3RDhemWjmrbPRXXf9qe6XOZXhxkQhAITkQ4pOPKSUrtkEfS9ZtBCDOxBvJXEhpJtW_ZZPFJYYEqNNAYWRAsn4gAAPYckYgikEYuS-za7hr0DYYNBjM5gNLhe5_d12e_ON1f9Ag';
        $checker = LicenseChecker::createFromLicenseAndKey($generated_license, self::PUBLIC_KEY);
        self::assertEquals(false, $checker->isValid());
        self::assertEquals(LicenseChecker::WRONG_DATA_PARTS, $checker->getErrorMessage());
    }
}
