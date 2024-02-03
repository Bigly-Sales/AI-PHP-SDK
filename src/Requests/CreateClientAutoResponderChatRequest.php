<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateClientAutoResponderChatRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly int|string $client_id,
        public readonly int|string $auto_responder_id,
        public readonly string $question,
        public readonly string $prompt,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->client_id}/auto-responders/{$this->auto_responder_id}/chat";
    }

    public function defaultBody(): array
    {
        return [
            'question' => $this->question,
            'prompt' => $this->prompt,
        ];
    }
}
