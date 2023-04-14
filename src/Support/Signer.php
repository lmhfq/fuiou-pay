<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/13
 * Time: 16:38
 */

namespace Lmh\Fuiou\Support;

class Signer
{

    public function sign(array $signArray): string
    {
        return md5(implode('|', $signArray));
    }

    /**
     * @param array $params
     * @param string $mchntKey
     * @return bool
     */
    public function verify(array $params,string $mchntKey): bool
    {
        $sign = $params['full_sign'];
        unset($params['full_sign'], $params['sign']);
        $params['mchnt_key'] = $mchntKey;
        return md5(implode('|', $params)) === $sign;
    }
}