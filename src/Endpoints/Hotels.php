<?php

namespace WeekBuzz\Endpoints;

use WeekBuzz\Routes;
use WeekBuzz\Endpoints\Endpoint;

class Hotels extends Endpoint
{

    /**
     * @param array $payload
     *
     * @return \ArrayObject
     */
    public function list()
    {
        return $this->client->request(
            self::GET,
            Routes::hotels()->list(),
            [
                'Content-Type' => "application/json",
            ]
        );
    }
}

