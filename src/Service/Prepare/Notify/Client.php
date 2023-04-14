<?php

namespace Lmh\Fuiou\Service\Prepare\Notify;

use Lmh\Fuiou\Entity\Order;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Service\Prepare\BaseClient;
use Lmh\Fuiou\Support\Signer;

class Client extends BaseClient
{
    /**
     * @param array $params
     * @return array
     * @throws InvalidArgumentException
     * @author lmh
     * @see parseNotify
     */
    public function parseNotify(array $params): array
    {
        $signArray = [];
        $signJoint = (array)($params['sign_joint'] ?? []);
        // $signJoint = [
        //            'result_code', 'result_msg', 'mchnt_cd', 'mchnt_order_no', 'settle_order_amt', 'order_amt', 'txn_fin_ts', 'reserved_fy_settle_dt', 'random_str',
        //            'sign', 'full_sign'
        //        ];
        foreach ($signJoint as $key => $item) {
            $param = $params[$item] ?? null;
            if (!$param) {
                throw new InvalidArgumentException('Notify sign params error ! ' . $item . ' is not null');
            }
            $signArray[$key] = $param;
        }
        (new Signer())->verify($signArray);
    }
}