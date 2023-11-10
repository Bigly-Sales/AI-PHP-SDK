<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class CreateClientRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(public string|int $reference_id, public array $files = [])
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/clients';
    }

    public function defaultConfig(): array
    {
        $multipart = [];

        foreach ($this->files as $k => $file) {
            $multipart[] = (new MultipartValue(
                name: "files[$k]",
                value: file_get_contents($file['path']),
                filename: $file['filename'],
                headers: [
                    'Content-Type' => 'text/plain',
                ]
            ))->toArray();
        }

        $multipart[] = [
            'name' => 'reference_id',
            'contents' => $this->reference_id,
        ];

        return [
            'multipart' => $multipart,
        ];
    }
}
