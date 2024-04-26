<?php

namespace violinist\LicenceCheck;

use Base64Url\Base64Url;
use Elliptic\EdDSA;
use MessagePack\BufferUnpacker;

class LicenceChecker
{

    const INVALID_SIGNATURE = 'Licence key signature is invalid';

    private $publicKey;
    private $licenceKey;
    private $errorMessage;
    private $valid;
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
        return $this->valid;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public static function createFromLicenceAndKey(string $licence_key, string $public_key): LicenceChecker
    {
        $instance = new self($public_key);
        $instance->licenceKey = $licence_key;
        try {
            $decoded = @Base64Url::decode($licence_key);
        }
        catch (\Throwable $e) {
            $instance->setError('Could not decode licence key');
        }
        try {
            $unzipped = @\gzinflate($decoded);
            if (!$unzipped) {
                $instance->setError('Could not uncompress licence key');
                return $instance;
            }
            $unpacker = new BufferUnpacker($unzipped);
            $data = $unpacker->unpackArray();
        }
        catch (\Throwable $e) {
            $instance->setError('Could not unpack licence key');
            return $instance;
        }
        try {
            if (count($data) !== 2) {
                $instance->setError('Licence key does not contain expected data parts');
                return $instance;
            }
            [$packed_payload, $signature] = $data;
            if (empty($packed_payload)) {
                $instance->setError('Licence key payload is empty');
                return $instance;
            }
            if (empty($signature)) {
                $instance->setError('Licence key signature is empty');
                return $instance;
            }
            $body = new BufferUnpacker($packed_payload);
            $payload = $body->unpack();
            $licence_object = @unserialize($payload, [
              'allowed_classes' => [Licence::class]
            ]);
            if (!$licence_object instanceof Licence) {
                $instance->setError('Licence key payload is not a valid licence object');
                return $instance;
            }
            $instance->payload = $licence_object;
            $ec = new EdDSA('ed25519');
            $key = @$ec->keyFromPublic($public_key);
            $instance->valid = @$key->verify(bin2hex($packed_payload), $signature);
            if (!$instance->valid) {
                $instance->setError(self::INVALID_SIGNATURE);
            }
        } catch (\Throwable $e) {
            $instance->setError('Could not verify licence key');
        }
        return $instance;
    }

}