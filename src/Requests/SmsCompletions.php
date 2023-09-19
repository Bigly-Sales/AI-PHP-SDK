<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use BiglySales\BiglySalesAiSdk\BiglySalesAi;

class SmsCompletions
{
    public function __construct(public readonly BiglySalesAi $connector)
    {
        //
    }

    public function create(string $pre_prompt, string $rules, array $payload = [])
    {
        return $this->connector->send(
            new CreateSmsCompletionRequest(
                $pre_prompt,
                $rules,
                $payload,
                $this->connector->api_key
            )
        );
    }
}
