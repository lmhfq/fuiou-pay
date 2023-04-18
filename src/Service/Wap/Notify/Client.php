<?php

namespace Lmh\Fuiou\Service\Wap\Notify;

use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Service\Wap\BaseClient;
use Lmh\Fuiou\Support\RsaUtil;

class Client extends BaseClient
{
    /**
     * @param array $params [mchnt_cd,message]
     * @return array
     * @throws InvalidArgumentException
     * @author lmh
     * @see parseNotify
     */
    public function parseNotify(array $params): array
    {
        $mchntCd = $params['mchnt_cd'] ?? '';
        $message = $params['message'] ?? '';
        $messageData = RsaUtil::privateDecrypt($message, $this->config->get('private_key'));
        return [
            'mchnt_cd' => $mchntCd,
            'message_data' => $messageData,
        ];
    }
}