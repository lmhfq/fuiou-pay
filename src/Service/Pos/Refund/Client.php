<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/13
 * Time: 17:12
 */

namespace Lmh\Fuiou\Service\Pos\Refund;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Lmh\Fuiou\Exceptions\FuiouPayException;
use Lmh\Fuiou\Exceptions\HttpException;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Service\Wap\BaseClient;

class Client extends BaseClient
{
    /**
     * 订单查询接口
     * @param array $params
     * @return Collection
     * @throws FuiouPayException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function create(array $params): Collection
    {
        $url = $this->getApi('/refund_transfer/aggposRefund.fuiou', 'Refund');
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $response = $this->request($url, $params, 'POST');
        return $response;
    }

    /**
     * @param array $params
     * @return Collection
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function refundQuery(array $params): Collection
    {
        $url = $this->getApi('/refund_transfer/aggposRefundQuery.fuiou', 'Refund');
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $response = $this->request($url, $params, 'POST');
        return $response;
    }
}