<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateEmailCompletionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $pre_prompt
     *  A pre-prompt is where you can provide information that should be taken into
     *  account when generating the email completion. Information placed here
     *  should be about the sender, not the recipient.
     * @param  string  $rules
     *  Rules are extra instructions you can give to fine-tune
     *  how the AI generates the email completion.
     * @param  array  $payload
     *  The payload is the information about the recipient.
     * @param  string  $api_key
     *  Your BiglySales AI API Key
     */
    public function __construct(
        public readonly string $pre_prompt,
        public readonly string $rules,
        public readonly array $payload,
        public readonly string $api_key
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/email-completions';
    }

    protected function defaultBody(): array
    {
        return [
            'pre_prompt' => $this->pre_prompt,
            'rules' => $this->rules,
            'payload' => $this->payload,
            'api_key' => $this->api_key,
        ];
    }
}
