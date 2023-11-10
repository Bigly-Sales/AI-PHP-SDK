<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ShowClientAutoResponderRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::GET;

    public function __construct(
        public readonly int|string $client_id,
        public readonly int|string $auto_responder_id
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->client_id}/auto-responders/{$this->auto_responder_id}";
    }
}
