<?php

namespace BiglySales\BiglySalesAiSdk;

use BiglySales\BiglySalesAiSdk\Requests\EmailCompletions;
use BiglySales\BiglySalesAiSdk\Requests\SmsCompletions;
use Saloon\Http\Connector;

class BiglySalesAi extends Connector
{
    public function __construct(public readonly string $api_key)
    {
        //
    }

    public function resolveBaseUrl(): string
    {
        return 'https://biglyai.test/api/v1';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function emailCompletions(): EmailCompletions
    {
        return new EmailCompletions($this);
    }

    public function smsCompletions(): SmsCompletions
    {
        return new SmsCompletions($this);
    }
}
