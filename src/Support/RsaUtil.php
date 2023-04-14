<?php

namespace Lmh\Fuiou\Support;

use Lmh\Fuiou\Exceptions\InvalidArgumentException;

class RsaUtil
{
    /**
     * @param $data
     * @param $publicKey
     * @return string
     * @throws InvalidArgumentException
     * @author lmh
     */
    public static function publicEncrypt($data, $publicKey): string
    {
        $encrypted = '';
        if (!$publicKey) {
            throw new InvalidArgumentException('平台公钥配置错误');
        }
        if (strpos($publicKey, '-----') === false) {
            $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($publicKey, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }
        $maxlength = 117;
        $output = '';
        while ($data) {
            $input = substr($data, 0, $maxlength);
            $data = substr($data, $maxlength);
            openssl_public_encrypt($input, $encrypted, $publicKey);
            $output .= $encrypted;
        }
        return base64_encode($output);
    }

    /**
     * @param $data
     * @param $privateKey
     * @return string
     * @throws InvalidArgumentException
     * @author lmh
     */
    public static function privateDecrypt($data, $privateKey): string
    {
        $decrypted = '';
        $data = base64_decode($data);
        if ($privateKey) {
            throw new InvalidArgumentException('签名证书配置错误');
        }
        openssl_private_decrypt(base64_decode($data), $decrypted, $privateKey, OPENSSL_PKCS1_OAEP_PADDING);
        return $decrypted;
    }
}