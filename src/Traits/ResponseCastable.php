<?php

namespace Lmh\Fuiou\Traits;

use Illuminate\Support\Collection;
use Lmh\Fuiou\Constant\ResultCode;
use Lmh\Fuiou\Exceptions\FuiouPayException;
use Psr\Http\Message\ResponseInterface;

trait ResponseCastable
{
    /**
     * @param ResponseInterface $response
     *
     * @return Collection
     */
    protected function castResponse(ResponseInterface $response): Collection
    {
        $response->getBody()->rewind();
        $contents = $response->getBody()->getContents();
        $response->getBody()->rewind();
        $array = json_decode($contents, true, 512, JSON_BIGINT_AS_STRING);
        var_dump($contents);exit;

        if (JSON_ERROR_NONE === json_last_error()) {
            return new Collection($array);
        }
        return new Collection();
    }

    /**
     * @throws FuiouPayException
     * @author lmh
     */
    public function checkResult(Collection $response)
    {
        if (isset($response['result_code']) && ResultCode::SUCCESS === $response['result_code']) {
            return;
        }
        $message = $response['result_msg'] ?? '系统错误';
        $code = $response['result_code'] ?? '';
        throw new FuiouPayException('[富友支付异常]异常代码：' . $code . ' 异常信息：' . $message, $code);
    }
}