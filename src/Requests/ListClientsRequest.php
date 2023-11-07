<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ListClientsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::GET;

    public function __construct(public readonly int $page = 0)
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/clients';
    }

    protected function defaultBody(): array
    {
        return [
            'page' => $this->page
        ];
    }
}
