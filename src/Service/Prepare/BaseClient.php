<?php

namespace Lmh\Fuiou\Service\Prepare;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Lmh\Fuiou\Constant\ResultCode;
use Lmh\Fuiou\Exceptions\FuiouPayException;
use Lmh\Fuiou\Exceptions\HttpException;
use Lmh\Fuiou\Exceptions\InvalidArgumentException;
use Lmh\Fuiou\Support\Config;
use Lmh\Fuiou\Support\Http;
use Lmh\Fuiou\Support\ServiceContainer;
use Lmh\Fuiou\Support\Signer;
use Lmh\Fuiou\Traits\ResponseCastable;


/**
 *
 */
class BaseClient
{
    use ResponseCastable;

    /**
     * API版本
     */
    public const API_VERSION = '1';

    /**
     * 终端号(没有真实终端号统一填88888888)
     */
    public const TERM_ID = '88888888';

    /**
     * 测试环境API地址
     */
    public const API_HOST_DEV = 'https://aipaytest.fuioupay.com';

    /**
     * 正式环境API地址
     */
    public const API_HOST_PRO = 'https://aipay.fuioupay.com';

    /**
     * 正式环境API地址
     */
    public const API_HOST_PRO_XS = 'https://aipay-xs.fuioupay.com';
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var
     */
    protected $http;

    /**
     */
    public function __construct(ServiceContainer $container)
    {

        $config = $container['config'] ?? [];
        $this->config = $config;
    }

    /**
     * @param string $api
     * @param array $params
     * @param string $method
     * @param array $options
     * @return Collection
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @author lmh
     */
    public function request(string $api, array $params, string $method = 'post', array $options = []): Collection
    {
        if (!isset($params['sign']) && isset($params['sign_joint'])) {
            $signArray = [];
            $signJoint = (array)$params['sign_joint'];
            unset($params['sign_joint']);
            foreach ($signJoint as $key => $item) {
                $param = $params[$item] ?? null;
                if (!$param) {
                    throw new InvalidArgumentException('参数错误： ' . $item . ' 是必须的');
                }
                $signArray[$key] = $param;
            }
            $params['sign'] = (new Signer())->sign($signArray);
        }
        $defaultOption = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($params, JSON_UNESCAPED_UNICODE)
        ];
        if (strtoupper($method) === 'POST') {
            $options = array_merge($defaultOption, $options);
            $response = $this->getHttp()->request($api, $method, $options);
            if ($response->getStatusCode() !== 200) {
                throw new HttpException('[富友支付异常]请求异常: HTTP状态码 ' . $response->getStatusCode());
            }
            return $this->castResponse($response);
        } else {
            $query = http_build_query($params);
            $response = [
                'redirect_url' => $api . $query,
            ];
            return new Collection($response);
        }
    }

    /**
     * @return array 解除参数
     */
    public function baseParams(): array
    {
        // 加载配置数据
        return array_merge(
            $this->config->only(['mchnt_cd', 'mchnt_key'])->toArray(),
            [
                'version' => self::API_VERSION,
                'random_str' => uniqid()
            ]
        );
    }

    /**
     * 请求客户端
     *
     * @return Http
     */
    public function getHttp(): Http
    {
        if (is_null($this->http)) {
            $this->http = new Http($this->config->get('http'));
        }

        return $this->http;
    }

    /**
     * 获取API地址
     *
     * @param string $api
     * @param bool $isXs
     * @return string
     */
    public function getApi(string $api, bool $isXs = false): string
    {
        if ($this->config->get('debug')) {
            return self::API_HOST_DEV . $api;
        } else {
            return $isXs ? self::API_HOST_PRO . $api : self::API_HOST_PRO_XS . $api;
        }
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
        throw new FuiouPayException('[支付异常]异常代码：' . $code . ' 异常信息：' . $message, $code);
    }
}