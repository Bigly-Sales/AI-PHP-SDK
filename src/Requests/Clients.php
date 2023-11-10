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

    public function list(int $page = 1)
    {
        return $this->connector->send(new ListClientsRequest($page));
    }

    public function show(string|int $id)
    {
        return $this->connector->send(new ShowClientRequest($id));
    }

    public function update(string|int $id, array $data = [])
    {
        return $this->connector->send(new UpdateClientRequest($id, $data));
    }

    public function delete(string|int $id)
    {
        return $this->connector->send(new DeleteClientRequest($id));
    }
}
