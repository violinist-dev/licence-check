<?php

namespace violinist\LicenseCheck;

use Base64Url\Base64Url;
use Elliptic\EdDSA;
use MessagePack\BufferUnpacker;

class LicenseChecker
{

    const INVALID_SIGNATURE = 'License key signature is invalid';
    const WRONG_DATA_PARTS = 'License key does not contain expected data parts';

    private $publicKey;
    private $licenseKey;
    private $errorMessage;
    private $valid;

    /**
     * @var License
     */
    private $payload;

    public function __construct(string $public_key)
    {
        $this->publicKey = $public_key;
    }

    public function setError(string $error)
    {
        $this->errorMessage = $error;
    }

    public function isValid() : bool
    {
        return (bool) $this->valid;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getPayload() : License
    {
        return $this->payload;
    }

    public static function createFromLicenseAndKey(string $license_key, string $public_key): LicenseChecker
    {
        $instance = new self($public_key);
        $instance->licenseKey = $license_key;
        try {
            $decoded = @Base64Url::decode($license_key);
        } catch (\Throwable $e) {
            $instance->setError('Could not decode license key');
        }
        try {
            $unzipped = @\gzinflate($decoded);
            if (!$unzipped) {
                $instance->setError('Could not uncompress license key');
                return $instance;
            }
            $unpacker = new BufferUnpacker($unzipped);
            $data = $unpacker->unpackArray();
        } catch (\Throwable $e) {
            $instance->setError('Could not unpack license key');
            return $instance;
        }
        try {
            if (count($data) !== 2) {
                $instance->setError(self::WRONG_DATA_PARTS);
                return $instance;
            }
            [$packed_payload, $signature] = $data;
            if (empty($packed_payload)) {
                $instance->setError('License key payload is empty');
                return $instance;
            }
            if (empty($signature)) {
                $instance->setError('License key signature is empty');
                return $instance;
            }
            $body = new BufferUnpacker($packed_payload);
            $payload = $body->unpack();
            $license_object = @unserialize($payload, [
              'allowed_classes' => [License::class],
            ]);
            if (!$license_object instanceof License) {
                $instance->setError('License key payload is not a valid license object');
                return $instance;
            }
            $instance->payload = $license_object;
            $ec = new EdDSA('ed25519');
            $key = @$ec->keyFromPublic($public_key);
            $instance->valid = @$key->verify(bin2hex($packed_payload), $signature);
            if (!$instance->valid) {
                $instance->setError(self::INVALID_SIGNATURE);
            }
        } catch (\Throwable $e) {
            $instance->setError('Could not verify license key');
        }
        return $instance;
    }
}
