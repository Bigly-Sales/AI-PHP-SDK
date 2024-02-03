<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use BiglySales\BiglySalesAiSdk\BiglySalesAi;

class ClientDocuments
{
    public function __construct(
        public readonly BiglySalesAi $connector,
        public readonly int|string $client_id
    ) {
        //
    }

    public function list(int $page = 1)
    {
        return $this->connector->send(new ListClientDocumentsRequest($this->client_id, $page));
    }

    public function create(array $files = [])
    {
        $this->connector->headers()->remove('Content-Type');

        return $this->connector->send(new CreateClientDocumentRequest($this->client_id, $files));
    }

    public function show(int $document_id)
    {
        return $this->connector->send(new ShowClientDocumentRequest($this->client_id, $document_id));
    }

    public function delete(int $document_id)
    {
        return $this->connector->send(new DeleteClientDocumentRequest($this->client_id, $document_id));
    }
}
