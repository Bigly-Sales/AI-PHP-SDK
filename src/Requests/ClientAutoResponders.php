<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use BiglySales\BiglySalesAiSdk\BiglySalesAi;
use BiglySales\BiglySalesAiSdk\Enums\AutoResponderType;

class ClientAutoResponders
{
    public function __construct(
        public readonly BiglySalesAi $connector,
        public readonly int|string $client_id
    ) {
        //
    }

    public function create(int|string $reference_id, AutoResponderType $type)
    {
        return $this->connector->send(new CreateClientAutoResponderRequest($this->client_id, $reference_id, $type));
    }

    public function chat(int|string $auto_responder_id, string $question, string $prompt)
    {
        return $this->connector->send(
            new CreateClientAutoResponderChatRequest(
                $this->client_id,
                $auto_responder_id,
                $question,
                $prompt
            )
        );
    }

    public function show(int|string $auto_responder_id)
    {
        return $this->connector->send(new ShowClientAutoResponderRequest($this->client_id, $auto_responder_id));
    }
}
