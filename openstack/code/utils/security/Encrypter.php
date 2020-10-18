<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
final class Encrypter
{
    /**
     * The encryption key.
     *
     * @var string
     */
    private $key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    private $cipher;

    /**
     * Encrypter constructor.
     * @param $key
     * @param string $cipher
     */
    public function __construct($key, $cipher = 'AES-128-CBC')
    {
        $key = (string) $key;

        if (strstr($key, 'base64:') !== false) {
            $key = base64_decode(substr($key, 7));
        }

        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) || ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     *
     * @throws EncryptException
     */
    public function encrypt($value)
    {
        $iv = random_bytes($this->getIvSize());

        $value = \openssl_encrypt(serialize($value), $this->cipher, $this->key, 0, $iv);

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        // Once we have the encrypted value we will go ahead base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.
        $mac = $this->hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (! is_string($json)) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws DecryptException
     */
    public function decrypt($payload)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $decrypted = \openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return unserialize($decrypted);
    }

    /**
     * Get the IV size for the cipher.
     *
     * @return int
     */
    protected function getIvSize()
    {
        return 16;
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  string  $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws  DecryptException
     */
    private function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $payload || $this->invalidPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        if (! $this->validMac($payload)) {
            throw new DecryptException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  array|mixed  $data
     * @return bool
     */
    private function invalidPayload($data)
    {
        return ! is_array($data) || ! isset($data['iv']) || ! isset($data['value']) || ! isset($data['mac']);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     *
     * @throws \RuntimeException
     */
    private function validMac(array $payload)
    {
        $bytes = random_bytes(16);

        $calcMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);

        return hash_equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calcMac);
    }

}