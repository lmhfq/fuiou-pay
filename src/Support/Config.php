<?php

namespace Lmh\Fuiou\Support;

use Illuminate\Support\Collection;

/**
 *
 */
class Config extends Collection
{
    public function __get($key)
    {
        return $this->get($key);
    }
}