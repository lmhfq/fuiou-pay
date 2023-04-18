<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/18
 * Time: 15:26
 */

namespace Lmh\Fuiou\Service\Pos\Transaction;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Lmh\Fuiou\Exceptions\HttpException;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Service\Wap\BaseClient;

class Client extends BaseClient
{

    /**
     * @param array $params
     * @return Collection
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function pay(array $params): Collection
    {
        $url = $this->getApi('/aggpos/order.fuiou','Transaction');
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
    public function query(array $params): Collection
    {
        $url = $this->getApi('/aggpos/orderQuery.fuiou','Transaction');
        $baseParams = $this->baseParams();
        $params = array_merge($params, $baseParams);
        $response = $this->request($url, $params, 'POST');
        return $response;
    }
}