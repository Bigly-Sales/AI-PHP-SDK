<?php

use BiglySales\BiglySalesAiSdk\BiglySalesAi;
use BiglySales\BiglySalesAiSdk\Enums\AutoResponderType;
use BiglySales\BiglySalesAiSdk\Requests\CreateClientAutoResponderChatRequest;
use BiglySales\BiglySalesAiSdk\Requests\CreateClientAutoResponderRequest;
use BiglySales\BiglySalesAiSdk\Requests\CreateClientDocumentRequest;
use BiglySales\BiglySalesAiSdk\Requests\CreateClientRequest;
use BiglySales\BiglySalesAiSdk\Requests\CreateEmailCompletionRequest;
use BiglySales\BiglySalesAiSdk\Requests\CreateSmsCompletionRequest;
use BiglySales\BiglySalesAiSdk\Requests\DeleteClientDocumentRequest;
use BiglySales\BiglySalesAiSdk\Requests\DeleteClientRequest;
use BiglySales\BiglySalesAiSdk\Requests\ListClientDocumentsRequest;
use BiglySales\BiglySalesAiSdk\Requests\ListClientsRequest;
use BiglySales\BiglySalesAiSdk\Requests\ObtainTokenRequest;
use BiglySales\BiglySalesAiSdk\Requests\ShowClientAutoResponderRequest;
use BiglySales\BiglySalesAiSdk\Requests\ShowClientDocumentRequest;
use BiglySales\BiglySalesAiSdk\Requests\ShowClientRequest;
use BiglySales\BiglySalesAiSdk\Requests\UpdateClientRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function(){

    $this->api_key = '84cf4ec6-372e-41f0-9a1d-7d4f71ab929e';

    $this->api = new BiglySalesAi($this->api_key);

    //$this->api->sender()->addMiddleware(function (callable $handler) {
    //    return function ($request, array $options) use ($handler) {
    //        dd($request->getBody()->getContents());
    //    };
    //});

    // $this->bearer_token = ObtainTokenRequest::make('sdk@test.com', 'password')->send()->json('token');

});

it('can produce email completion', function () {
    
    $parsed_output = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
    ];

    $completion = sprintf('Here is the JSON: %s', json_encode($parsed_output));

    $mockClient = new MockClient([
        BiglySalesAi::class => MockResponse::make($this->api_key, 200),
        CreateEmailCompletionRequest::class => MockResponse::make([
            'trace_id'      => 'd380611e-c32a-486b-8459-489c02fd2ce6',
            'info'          => 'Given that AI can sometimes hallucinate we have provided you the full completion in addition to a parsed',
            'completion'    => $completion,
            'parsed_output' => $parsed_output,
        ], 200),
        ObtainTokenRequest::class => MockResponse::make('13|wTocXqXDIcJpPEsDlSaShuhqYn3fDbA5npDC9mr8', 200),
    ]);

    $this->api->withMockClient($mockClient);

    // A pre-prompt is where you can provide information that should be taken into
    // account when generating the email completion. Information placed here
    // should be about the sender, not the recipient.
    $pre_prompt = <<<'EOT'
    Here is information about our company:
    - Company Name: John Doe Payment Services, LLC.
    - Services: High Risk Payment Processing
    - Contact: john@johndoe.com, 888-999-1234
    - Calendly: https://calendly.com/johndoe
    EOT;

    // Rules are extra instructions you can give to fine-tune
    // how the AI generates the email completion.
    $rules = <<<'EOT'
    - Place recipients name at the beginning of the subject.
    - Ask they're availability for a call on Friday.
    - Address recipient by first name only.
    - Include my contact into in the signature, including my Calendly link.
    EOT;

    // The payload is the information about the recipient.
    $payload = [
        'name' => 'Jane Smith',
        'email' => 'jane@janesmith.com',
        'title' => 'Founder',
        'company' => "Jane's Resume Services",
        'description' => 'Resume writing service.',
    ];

    $response = $this->api->emailCompletions()->create($pre_prompt, $rules, $payload);

    expect($response->json('trace_id'))->toBeString();
    expect($response->json('info'))->toBeString();
    expect($response->json('completion'))->toBeString();
    expect($response->json('parsed_output'))->toBeArray();
});


it('can produce sms completion', function () {

    $parsed_output = [
        'message' => 'Test Message',
    ];

    $completion = sprintf('Here is the JSON: %s', json_encode($parsed_output));

    $mockClient = new MockClient([
        BiglySalesAi::class => MockResponse::make($this->api_key, 200),
        CreateSmsCompletionRequest::class => MockResponse::make([
            'trace_id' => 'd380611e-c32a-486b-8459-489c02fd2ce6',
            'info' => 'Given that AI can sometimes hallucinate we have provided you the full completion in addition to a parsed',
            'completion' => $completion,
            'parsed_output' => $parsed_output,
        ], 200),
        ObtainTokenRequest::class => MockResponse::make('13|wTocXqXDIcJpPEsDlSaShuhqYn3fDbA5npDC9mr8', 200),
    ]);

    $this->api->withMockClient($mockClient);

    // A pre-prompt is where you can provide information that should be taken into
    // account when generating the sms completion. Information placed here
    // should be about the sender, not the recipient.
    $pre_prompt = <<<'EOT'
    Here is information about our company:
    - Company Name: John Doe Payment Services, LLC.
    - Services: High Risk Payment Processing
    - Contact: john@johndoe.com, 888-999-1234
    - Calendly: https://calendly.com/johndoe
    EOT;

    // Rules are extra instructions you can give to fine-tune
    // how the AI generates the sms completion.
    $rules = <<<'EOT'
    - Should be no more than 100 characters long.
    EOT;

    // The payload is the information about the recipient.
    $payload = [
        'name' => 'Jane Smith',
        'email' => 'jane@janesmith.com',
        'title' => 'Founder',
        'company' => "Jane's Resume Services",
        'description' => 'Resume writing service.',
    ];

    $response = $this->api->smsCompletions()->create($pre_prompt, $rules, $payload);

    expect($response->json('trace_id'))->toBeString();
    expect($response->json('info'))->toBeString();
    expect($response->json('completion'))->toBeString();
    expect($response->json('parsed_output'))->toBeArray();
});

it('can create client', function () {

    $response_data = [
        "data" => [
            "id" => 1,
            "api_organization_id" => 1,
            "reference_id" => "test-654c18aacfddf",
            "name" => null,
            "created_at" => "2023-11-09T20:31:19.000000Z",
            "updated_at" => "2023-11-09T20:31:19.000000Z",
            "deleted_at" => null,
            "documents" => [
                [
                    "id" => 7,
                    "documentable_type" => "App\Models\ApiClient",
                    "documentable_id" => 1,
                    "name" => "test-file.txt",
                    "path" => "31/T7FuXpMnCqkSzgvTS1IM7qYAvWHqQosPRreFdgTp.txt",
                    "mime" => "text/plain",
                    "extension" => "txt",
                    "created_at" => "2023-11-09T20:31:19.000000Z",
                    "updated_at" => "2023-11-09T20:31:19.000000Z",
                    "deleted_at" => null,
                    "links" => [
                        'api' => [
                            'show' => 'https://biglyai.test/api/v1/documents/1',
                            'download' => 'https://biglyai.test/api/v1/documents/1/download',
                        ],
                        "ui" => []
                    ],
                ],
            ],
        ],
        "links" => [
            "api" => [
                "show" => "https://biglyai.test/api/v1/clients/1",
                "update" => "https://biglyai.test/api/v1/clients/1",
            ],
            "ui" => [],
        ],
    ];

    $mockClient = new MockClient([
        CreateClientRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clients()->create(
        uniqid('test-'),
        [
            [
                'name'      => 'files',
                'filename'  => 'test-file.txt',
                'path'      => __DIR__.'/tmp/test-file.txt',
            ],
        ]
    );

    expect($response->json('data'))->toBeArray();
    expect($response->json('data'))->toMatchArray($response_data['data']);

});

it('can list clients', function () {

    $response_data = [
        'data' => [
            [
                "id" => 1,
                "api_organization_id" => 1,
                "reference_id" => "Y2u7hCc7yKDZmlTN",
                "name" => null,
                "created_at" => "2023-11-07T20:55:26.000000Z",
                "updated_at" => "2023-11-07T20:55:26.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/1",
                        "update" => "https://biglyai.test/api/v1/clients/1"
                    ],
                    "ui" => []
                ]
            ]
        ]
    ];

    $mockClient = new MockClient([
        ListClientsRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clients()->list();

    expect($response->json())->toEqual($response_data);
});

it('can show client', function () {

    $response_data = [
        'data' => [
            "id" => 1,
            "api_organization_id" => 1,
            "reference_id" => "Y2u7hCc7yKDZmlTN",
            "name" => null,
            "created_at" => "2023-11-07T20:55:26.000000Z",
            "updated_at" => "2023-11-07T20:55:26.000000Z",
            "deleted_at" => null,
            "links" => [
                "api" => [
                    "show" => "https://biglyai.test/api/v1/clients/1",
                    "update" => "https://biglyai.test/api/v1/clients/1"
                ],
                "ui" => []
            ]
        ]
    ];

    $mockClient = new MockClient([
        ShowClientRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clients()->show(1);

    expect($response->json())->toEqual($response_data);
});

it('can update client', function () {

    $response_data = [
        'data' => [
            "id" => 1,
            "api_organization_id" => 1,
            "reference_id" => "new-reference-id",
            "name" => null,
            "created_at" => "2023-11-07T20:55:26.000000Z",
            "updated_at" => "2023-11-07T20:55:26.000000Z",
            "deleted_at" => null,
            "links" => [
                "api" => [
                    "show" => "https://biglyai.test/api/v1/clients/1",
                    "update" => "https://biglyai.test/api/v1/clients/1"
                ],
                "ui" => []
            ]
        ]
    ];

    $mockClient = new MockClient([
        UpdateClientRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clients()->update(1, ['reference_id' => 'new-reference-id']);

    expect($response->json())->toEqual($response_data);
});


it('can delete client', function () {

    $response_data = [
        'data' => [
            "id" => 1,
            "api_organization_id" => 1,
            "reference_id" => "new-reference-id",
            "name" => null,
            "created_at" => "2023-11-07T20:55:26.000000Z",
            "updated_at" => "2023-11-07T20:55:26.000000Z",
            "deleted_at" => "2023-11-07T20:56:12.000000Z",
            "links" => [
                "api" => [
                    "show" => "https://biglyai.test/api/v1/clients/1",
                    "update" => "https://biglyai.test/api/v1/clients/1"
                ],
                "ui" => []
            ]
        ]
    ];

    $mockClient = new MockClient([
        DeleteClientRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clients()->delete(1);

    expect($response->json())->toEqual($response_data);
});


it('can list client documents', function () {

    $response_data = [
        "data" => [
            [
                "id" => 2,
                "documentable_type" => "App\Models\ApiClient",
                "documentable_id" => 26,
                "name" => "test-file.txt",
                "path" => "26/a8dcN2RLCP7gLCemZkQV6eumDU8T5nXxZiCm9ajT.txt",
                "mime" => "text/plain",
                "extension" => "txt",
                "created_at" => "2023-11-09T13:27:42.000000Z",
                "updated_at" => "2023-11-09T13:27:42.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/26/documents/2",
                        "delete" => "https://biglyai.test/api/v1/clients/26/documents/2",
                    ],
                    "ui" => [],
                ],
            ],
        ],
        "links" => [
            "first" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
            "last" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
            "prev" => null,
            "next" => null,
        ],
        "meta" => [
            "current_page" => 1,
            "from" => 1,
            "last_page" => 1,
            "links" => [
                [
                    "url" => null,
                    "label" => "&laquo; Previous",
                    "active" => false,
                ],
                [
                    "url" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
                    "label" => "1",
                    "active" => true,
                ],
                [
                    "url" => null,
                    "label" => "Next &raquo;",
                    "active" => false,
                ],
            ],
            "path" => "https://biglyai.test/api/v1/clients/26/documents",
            "per_page" => 15,
            "to" => 1,
            "total" => 1,
        ],
    ];

    $mockClient = new MockClient([
        ListClientDocumentsRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clientDocuments(26)->list();

    expect($response->json())->toEqual($response_data);
});


it('can create client document', function () {

    $response_data = [
        "data" => [
            [
                "id" => 8,
                "documentable_type" => "App\Models\ApiClient",
                "documentable_id" => 26,
                "name" => "test-file.txt",
                "path" => "26/bDVh3tZFD8gjwllLm3UL2MNa18X4vHciUukfkKq5.txt",
                "mime" => "text/plain",
                "extension" => "txt",
                "created_at" => "2023-11-09T23:17:55.000000Z",
                "updated_at" => "2023-11-09T23:17:55.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/26/documents/8",
                        "delete" => "https://biglyai.test/api/v1/clients/26/documents/8",
                    ],
                    "ui" => [],
                ],
            ],
            [
                "id" => 9,
                "documentable_type" => "App\Models\ApiClient",
                "documentable_id" => 26,
                "name" => "test-file.txt",
                "path" => "26/BXxKBDXJBvxXCqI7OupoQgOvr3MCCTbN2dBLXhlW.txt",
                "mime" => "text/plain",
                "extension" => "txt",
                "created_at" => "2023-11-09T23:17:55.000000Z",
                "updated_at" => "2023-11-09T23:17:55.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/26/documents/9",
                        "delete" => "https://biglyai.test/api/v1/clients/26/documents/9",
                    ],
                    "ui" => [],
                ],
            ],
            [
                "id" => 10,
                "documentable_type" => "App\Models\ApiClient",
                "documentable_id" => 26,
                "name" => "test-file.txt",
                "path" => "26/6iXK3V2N3w0q3rk7v8BNSSTPKYF2QU6oQw59idMP.txt",
                "mime" => "text/plain",
                "extension" => "txt",
                "created_at" => "2023-11-09T23:17:55.000000Z",
                "updated_at" => "2023-11-09T23:17:55.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/26/documents/10",
                        "delete" => "https://biglyai.test/api/v1/clients/26/documents/10",
                    ],
                    "ui" => [],
                ],
            ],
            [
                "id" => 11,
                "documentable_type" => "App\Models\ApiClient",
                "documentable_id" => 26,
                "name" => "test-file.txt",
                "path" => "26/WE51M3qMqhWjVlz480zdIXM9w13lDnHTG2FoC8qY.txt",
                "mime" => "text/plain",
                "extension" => "txt",
                "created_at" => "2023-11-09T23:17:55.000000Z",
                "updated_at" => "2023-11-09T23:17:55.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/26/documents/11",
                        "delete" => "https://biglyai.test/api/v1/clients/26/documents/11",
                    ],
                    "ui" => [],
                ],
            ],
            [
                "id" => 12,
                "documentable_type" => "App\Models\ApiClient",
                "documentable_id" => 26,
                "name" => "test-file.txt",
                "path" => "26/i4PjDH63djYqxDIs2PsXSmShmHRGcBh8urNpvuir.txt",
                "mime" => "text/plain",
                "extension" => "txt",
                "created_at" => "2023-11-09T23:17:55.000000Z",
                "updated_at" => "2023-11-09T23:17:55.000000Z",
                "deleted_at" => null,
                "links" => [
                    "api" => [
                        "show" => "https://biglyai.test/api/v1/clients/26/documents/12",
                        "delete" => "https://biglyai.test/api/v1/clients/26/documents/12",
                    ],
                    "ui" => [],
                ],
            ],
        ],
    ];

    $mockClient = new MockClient([
        CreateClientDocumentRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $file = [
        'name'     => 'files',
        'filename' => 'test-file.txt',
        'path'     => __DIR__.'/tmp/test-file.txt',
    ];

    $files[] = $file;
    $files[] = $file;
    $files[] = $file;
    $files[] = $file;
    $files[] = $file;

    $response = $this->api->clientDocuments(26)->create($files);

    expect($response->json())->toEqual($response_data);

});

it('can show client document', function () {

    $response_data = [
        "data" => [
            "id" => 2,
            "documentable_type" => "App\Models\ApiClient",
            "documentable_id" => 26,
            "name" => "test-file.txt",
            "path" => "26/a8dcN2RLCP7gLCemZkQV6eumDU8T5nXxZiCm9ajT.txt",
            "mime" => "text/plain",
            "extension" => "txt",
            "created_at" => "2023-11-09T13:27:42.000000Z",
            "updated_at" => "2023-11-09T13:27:42.000000Z",
            "deleted_at" => null,
            "links" => [
                "api" => [
                    "show" => "https://biglyai.test/api/v1/clients/26/documents/2",
                    "delete" => "https://biglyai.test/api/v1/clients/26/documents/2",
                ],
                "ui" => [],
            ],
        ],
        "links" => [
            "first" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
            "last" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
            "prev" => null,
            "next" => null,
        ],
        "meta" => [
            "current_page" => 1,
            "from" => 1,
            "last_page" => 1,
            "links" => [
                [
                    "url" => null,
                    "label" => "&laquo; Previous",
                    "active" => false,
                ],
                [
                    "url" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
                    "label" => "1",
                    "active" => true,
                ],
                [
                    "url" => null,
                    "label" => "Next &raquo;",
                    "active" => false,
                ],
            ],
            "path" => "https://biglyai.test/api/v1/clients/26/documents",
            "per_page" => 15,
            "to" => 1,
            "total" => 1,
        ],
    ];

    $mockClient = new MockClient([
        ShowClientDocumentRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clientDocuments(26)->show(2);

    expect($response->json())->toEqual($response_data);
});


it('can delete client document', function () {

    $response_data = [
        "data" => [
            "id" => 2,
            "documentable_type" => "App\Models\ApiClient",
            "documentable_id" => 26,
            "name" => "test-file.txt",
            "path" => "26/a8dcN2RLCP7gLCemZkQV6eumDU8T5nXxZiCm9ajT.txt",
            "mime" => "text/plain",
            "extension" => "txt",
            "created_at" => "2023-11-09T13:27:42.000000Z",
            "updated_at" => "2023-11-09T13:27:42.000000Z",
            "deleted_at" => "2023-11-09T13:28:42.000000Z",
            "links" => [
                "api" => [
                    "show" => "https://biglyai.test/api/v1/clients/26/documents/2",
                    "delete" => "https://biglyai.test/api/v1/clients/26/documents/2",
                ],
                "ui" => [],
            ],
        ],
        "links" => [
            "first" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
            "last" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
            "prev" => null,
            "next" => null,
        ],
        "meta" => [
            "current_page" => 1,
            "from" => 1,
            "last_page" => 1,
            "links" => [
                [
                    "url" => null,
                    "label" => "&laquo; Previous",
                    "active" => false,
                ],
                [
                    "url" => "https://biglyai.test/api/v1/clients/26/documents?page=1",
                    "label" => "1",
                    "active" => true,
                ],
                [
                    "url" => null,
                    "label" => "Next &raquo;",
                    "active" => false,
                ],
            ],
            "path" => "https://biglyai.test/api/v1/clients/26/documents",
            "per_page" => 15,
            "to" => 1,
            "total" => 1,
        ],
    ];

    $mockClient = new MockClient([
        DeleteClientDocumentRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clientDocuments(26)->delete(2);

    expect($response->json())->toEqual($response_data);
});


it('can create client auto responders', function () {

    $response_data = [
        "data" => [
            "hash" => "tTdsMcMlEfiGUziGVbycczaI4INGv0MF",
            "type" => "chatbot",
            "reference_id" => "abc-123-456",
            "api_client_id" => 26,
            "updated_at" => "2023-11-09T23:58:37.000000Z",
            "created_at" => "2023-11-09T23:58:37.000000Z",
            "id" => 1,
            "links" => [
                "self" => "https://biglyai.test/api/v1/clients/26/auto-responders/1",
                "chat" => "https://biglyai.test/api/v1/clients/26/auto-responders/1/chat",
            ],
        ],
    ];

    $mockClient = new MockClient([
        CreateClientAutoResponderRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $reference_id = 'abc-123-456';
    $type         = AutoResponderType::CHATBOT;

    $response = $this->api->clientAutoResponders(26)->create($reference_id, $type);

    expect($response->json())->toEqual($response_data);

});


it('can show client auto responder', function () {

    $response_data = [
        "data" => [
            "log" => [
                [
                    "id" => 60,
                    "chatable_type" => "App\Models\ApiAutoResponder",
                    "chatable_id" => 2,
                    "type" => "question",
                    "content" => "Hi, how are you?",
                    "created_at" => "2023-11-10T00:13:36.000000Z",
                    "updated_at" => "2023-11-10T00:13:36.000000Z",
                    "deleted_at" => null,
                ],
                [
                    "id" => 61,
                    "chatable_type" => "App\Models\ApiAutoResponder",
                    "chatable_id" => 2,
                    "type" => "answer",
                    "content" => "I'm fine.",
                    "created_at" => "2023-11-10T00:13:53.000000Z",
                    "updated_at" => "2023-11-10T00:13:53.000000Z",
                    "deleted_at" => null,
                ],
            ],
        ],
    ];

    $mockClient = new MockClient([
        ShowClientAutoResponderRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $response = $this->api->clientAutoResponders(26)->show(2);

    expect($response->json())->toEqual($response_data);

});


it('can chat with client auto responder', function () {

    $response_data = [
        'data' => [
            'response' => "I'm fine.",
            'error'    => ''
        ]
    ];

    $mockClient = new MockClient([
        CreateClientAutoResponderChatRequest::class => MockResponse::make($response_data)
    ]);

    $this->api->withMockClient($mockClient);

    $question = 'Hi, how are you?';
    $prompt =<<<EOT
    If asked how you are respond: "I'm fine."
    EOT;

    $response = $this->api->clientAutoResponders(26)->chat(2, $question, $prompt);

    expect($response->json())->toEqual($response_data);

});