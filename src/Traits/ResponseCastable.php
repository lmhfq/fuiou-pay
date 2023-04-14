<?php

namespace Lmh\Fuiou\Traits;

use Illuminate\Support\Collection;
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
        if (JSON_ERROR_NONE === json_last_error()) {
            return new Collection($array);
        }
        return new Collection();
    }
}