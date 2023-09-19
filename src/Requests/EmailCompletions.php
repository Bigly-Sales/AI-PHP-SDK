<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use BiglySales\BiglySalesAiSdk\BiglySalesAi;

class EmailCompletions
{
    public function __construct(public readonly BiglySalesAi $connector)
    {
        //
    }

    public function create(string $pre_prompt, string $rules, array $payload = [])
    {
        return $this->connector->send(
            new CreateEmailCompletionRequest(
                $pre_prompt,
                $rules,
                $payload,
                $this->connector->api_key
            )
        );
    }
}