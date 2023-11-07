<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasJsonBody;

class ObtainTokenRequest extends SoloRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return 'https://biglyai.test/api/token';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}