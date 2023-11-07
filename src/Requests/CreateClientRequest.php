<?php

namespace BiglySales\BiglySalesAiSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Traits\Body\HasMultipartBody;

class CreateClientRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(public string|int $reference_id, public array $files = [])
    {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/clients';
    }

    protected function defaultHeaders(): array
    {
        $boundary = md5(uniqid());

        return [
            //'Content-Type' => "multipart/form-data; boundary=$boundary; charset=utf-8"
            'Content-Type' => "multipart; boundary=$boundary; charset=utf-8"
        ];
    }

    protected function buildMultipartValues(): array
    {
        $multipart_values = [];

        foreach($this->files as $file)
        {
            $multipart_values[] = new MultipartValue(
                    name: $file['name'],
                   value: file_get_contents($file['path']),
                filename: $file['filename'],
                 headers: [
                    'Content-Type' => 'text/plain'
                ]
            );
        }

        return $multipart_values;
    }

    protected function defaultBody(): array
    {
        $default = [new MultipartValue(name: 'reference_id', value: $this->reference_id)];
        $files   = $this->buildMultipartValues();

        return $default + $files;
    }
}
