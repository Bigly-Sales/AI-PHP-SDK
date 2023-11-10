<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use BiglySales\BiglySalesAiSdk\Enums\AutoResponderType;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateClientAutoResponderRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string|int $client_id,
        public readonly int|string $reference_id,
        public readonly AutoResponderType $type,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->client_id}/auto-responders";
    }

    public function defaultBody(): array
    {
        return [
            'reference_id' => $this->reference_id,
            'type' => $this->type->value,
        ];
    }
}
