<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: lmh <lmh@weiyian.com>
 * Date: 2023/4/13
 * Time: 18:11
 */

namespace Lmh\Fuiou\Support;

use Pimple\Container;

class ServiceContainer extends Container
{
    /**
     * @var array
     */
    protected $providers = [];
    /**
     * @var array
     */
    protected $userConfig = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct();
        $this->userConfig = $config;
        $this->registerProviders($this->getProviders());
        $this->logConfiguration($config);
    }

    /**
     * Log configuration.
     *
     * @param array $config
     */
    public function logConfiguration(array $config)
    {
        $config = new Config($config);
        $keys = ['mchnt_cd', 'ins_cd', 'private_key', 'environment', 'mchnt_key'];
        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***' . substr($config[$key], -5);
        }
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return ServiceContainer
     */
    public function addProvider(string $provider): ServiceContainer
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders(): array
    {
        return array_merge([
            ConfigServiceProvider::class,
        ], $this->providers);
    }

    /**
     * @return array
     * @author lmh
     */
    public function getDefaultConfig(): array
    {
        $base = [
            'http' => [
                'timeout' => 60.0,
            ],
            'debug' => true,
            'mchnt_cd' => '',
            'ins_cd' => '',
            'mchnt_key' => '',
            'private_key' => '',
            'public_key' => '',
            'fuiou_public_key' => '',
        ];
        return array_replace_recursive($base, $this->userConfig);
    }

    /**
     * Register providers.
     */
    private function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get(string $id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed $value
     */
    public function __set(string $id, $value)
    {
        $this->offsetSet($id, $value);
    }
}