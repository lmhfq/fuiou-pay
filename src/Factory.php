<?php
declare(strict_types=1);


namespace Lmh\Fuiou;


use Illuminate\Support\Str;

/**
 * Class Factory
 * @package Lmh\Cpcn
 * User: lmh <lmh@weiyian.com>
 * Date: 2022/1/22
 * @method static Service\Wap\Application    wap(array $config)
 * @method static Service\Prepare\Application    prepare(array $config)
 * @method static Service\Pos\Application    pos(array $config)
 */
class Factory
{
    /**
     * @param string $name
     * @param array $config
     * @return mixed
     * @author lmh
     */
    public static function make(string $name, array $config)
    {
        $name = Str::studly($name);
        $application = "\\Lmh\\Fuiou\\Service\\{$name}\\Application";
        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return self::make($name, ...$arguments);
    }
}