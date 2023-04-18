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
     * @param string $source
     * @param $privateKey
     * @return string
     * @throws InvalidArgumentException
     * @author lmh
     */
    public static function privateDecrypt(string $source, $privateKey): string
    {
        if (!$privateKey) {
            throw new InvalidArgumentException('签名证书配置错误');
        }
        $source = base64_decode($source);
        if (strpos($privateKey, '-----') === false) {
            $privateKey = "-----BEGIN PRIVATE KEY-----\n" .
                wordwrap($privateKey, 64, "\n", true) .
                "\n-----END PRIVATE KEY-----";
        }
        $iD = openssl_get_privatekey($privateKey);
        $maxlength = openssl_pkey_get_details($iD)['bits'] ?? 0;
        $maxlength = $maxlength / 8;
        $output = '';
        while ($source) {
            $input = substr($source, 0, $maxlength);
            $source = substr($source, $maxlength);
            $decrypted = '';
            openssl_private_decrypt($input, $decrypted, $privateKey);
            $output .= $decrypted;
        }
        return $output;
    }
}