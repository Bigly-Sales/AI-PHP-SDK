<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use BiglySales\BiglySalesAiSdk\BiglySalesAi;

class Clients
{
    public function __construct(public readonly BiglySalesAi $connector)
    {
        //
    }

    public function create(string|int $reference_id, array $files = [])
    {
        return $this->connector->send(new CreateClientRequest($reference_id, $files));
    }

    public function list()
    {
        return $this->connector->send(new ListClientsRequest);
    }
}