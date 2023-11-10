<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ShowClientRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::GET;

    public function __construct(public string|int $id)
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->id}";
    }
}
