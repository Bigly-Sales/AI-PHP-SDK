<?php

namespace BiglySales\BiglySalesAiSdk;

use BiglySales\BiglySalesAiSdk\Requests\Clients;
use BiglySales\BiglySalesAiSdk\Requests\EmailCompletions;
use BiglySales\BiglySalesAiSdk\Requests\SmsCompletions;
use BiglySales\BiglySalesAiSdk\Requests\Tokens;
use Saloon\Http\Connector;

class BiglySalesAi extends Connector
{
    public function __construct(
        public readonly string $api_key,
        public readonly ?string $bearer_token = null
    ) {
        if($this->bearer_token){
            $this->withTokenAuth($this->bearer_token);
        }
    }

    public function resolveBaseUrl(): string
    {
        return 'https://biglyai.test/api/v1';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'BIGLY-API-KEY' => $this->api_key
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

    public function clients()
    {
        return new Clients($this);
    }
}
