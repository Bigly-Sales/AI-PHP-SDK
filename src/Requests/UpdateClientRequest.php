<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateClientRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(public string|int $id, public array $payload = [])
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->id}";
    }

    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
