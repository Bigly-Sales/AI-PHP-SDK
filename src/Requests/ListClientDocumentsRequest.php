<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ListClientDocumentsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::GET;

    public function __construct(public int|string $client_id, public readonly int $page = 1)
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->client_id}/documents";
    }

    protected function defaultBody(): array
    {
        return [
            'page' => $this->page,
        ];
    }
}
