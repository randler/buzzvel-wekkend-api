<?php

namespace WeekBuzz\Endpoints;

use WeekBuzz\Search;

abstract class Endpoint
{
    /**
     * @var string
     */
    const POST = 'POST';
    /**
     * @var string
     */
    const GET = 'GET';
    /**
     * @var string
     */
    const PUT = 'PUT';
    /**
     * @var string
     */
    const DELETE = 'DELETE';

    /**
     * @var \WeekBuzz\Search
     */
    protected $client;

    /**
     * @param \WeekBuzz\Search $client
     */
    public function __construct(Search $client)
    {
        $this->client = $client;
    }
}
