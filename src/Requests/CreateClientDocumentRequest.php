<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class CreateClientDocumentRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(public readonly string|int $client_id, public readonly array $files = [])
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/clients/{$this->client_id}/documents";
    }

    public function defaultConfig(): array
    {
        $multiparts = [];

        foreach($this->files as $k => $file)
        {
            $multiparts[] = (new MultipartValue(
                    name: "files[$k]",
                   value: file_get_contents($file['path']),
                filename: $file['filename'],
                 headers: [
                    'Content-Type' => 'text/plain',
                ]
            ))->toArray();
        }

        return [
            'multipart' => $multiparts
        ];
    }
}
