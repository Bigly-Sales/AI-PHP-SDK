<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ShowClientDocumentRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::GET;

    public function __construct(
        public readonly int|string $client_id,
        public readonly int|string $document_id
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->client_id}/documents/{$this->document_id}";
    }
}
