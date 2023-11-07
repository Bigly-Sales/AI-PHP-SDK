<?php

use BiglySales\BiglySalesAiSdk\BiglySalesAi;
use BiglySales\BiglySalesAiSdk\Requests\CreateEmailCompletionRequest;
use BiglySales\BiglySalesAiSdk\Requests\CreateSmsCompletionRequest;
use BiglySales\BiglySalesAiSdk\Requests\ObtainTokenRequest;
use Illuminate\Support\Facades\Storage;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;


it('can produce email completion', function () {

    $api_key = '84cf4ec6-372e-41f0-9a1d-7d4f71ab929e';

    $parsed_output = [
        'subject' => 'Test Subject',
        'body' => 'Test Body',
    ];

    $completion = sprintf('Here is the JSON: %s', json_encode($parsed_output));

    $mockClient = new MockClient([
        BiglySalesAi::class => MockResponse::make($api_key, 200),
        CreateEmailCompletionRequest::class => MockResponse::make([
            'trace_id' => 'd380611e-c32a-486b-8459-489c02fd2ce6',
            'info' => 'Given that AI can sometimes hallucinate we have provided you the full completion in addition to a parsed',
            'completion' => $completion,
            'parsed_output' => $parsed_output,
        ], 200),
        ObtainTokenRequest::class => MockResponse::make('13|wTocXqXDIcJpPEsDlSaShuhqYn3fDbA5npDC9mr8', 200)
    ]);

    $bearer_token = ObtainTokenRequest::make('sdk@test.com', 'password')
                        ->send()
                        ->json('token');

    $api = new BiglySalesAi($api_key, $bearer_token);
    $api->withMockClient($mockClient);

     # A pre-prompt is where you can provide information that should be taken into
     # account when generating the email completion. Information placed here
     # should be about the sender, not the recipient.
    $pre_prompt = <<<'EOT'
    Here is information about our company:
    - Company Name: John Doe Payment Services, LLC.
    - Services: High Risk Payment Processing
    - Contact: john@johndoe.com, 888-999-1234
    - Calendly: https://calendly.com/johndoe
    EOT;

     # Rules are extra instructions you can give to fine-tune
     # how the AI generates the email completion.
    $rules = <<<'EOT'
    - Place recipients name at the beginning of the subject.
    - Ask they're availability for a call on Friday.
    - Address recipient by first name only.
    - Include my contact into in the signature, including my Calendly link.
    EOT;

    # The payload is the information about the recipient.
    $payload = [
        'name' => 'Jane Smith',
        'email' => 'jane@janesmith.com',
        'title' => 'Founder',
        'company' => "Jane's Resume Services",
        'description' => 'Resume writing service.',
    ];

    $response = $api->emailCompletions()->create($pre_prompt, $rules, $payload);

    expect($response->json('trace_id'))->toBeString();
    expect($response->json('info'))->toBeString();
    expect($response->json('completion'))->toBeString();
    expect($response->json('parsed_output'))->toBeArray();
});


it('can produce sms completion', function () {

    $api_key = '84cf4ec6-372e-41f0-9a1d-7d4f71ab929e';

    $parsed_output = [
        'message' => 'Test Message',
    ];

    $completion = sprintf('Here is the JSON: %s', json_encode($parsed_output));

    $mockClient = new MockClient([
        BiglySalesAi::class => MockResponse::make($api_key, 200),
        CreateSmsCompletionRequest::class => MockResponse::make([
            'trace_id' => 'd380611e-c32a-486b-8459-489c02fd2ce6',
            'info' => 'Given that AI can sometimes hallucinate we have provided you the full completion in addition to a parsed',
            'completion' => $completion,
            'parsed_output' => $parsed_output,
        ], 200),
        ObtainTokenRequest::class => MockResponse::make('13|wTocXqXDIcJpPEsDlSaShuhqYn3fDbA5npDC9mr8', 200)
    ]);

    $bearer_token = ObtainTokenRequest::make('sdk@test.com', 'password')
                    ->send()
                    ->json('token');

    $api = new BiglySalesAi($api_key, $bearer_token);
    $api->withMockClient($mockClient);

    # A pre-prompt is where you can provide information that should be taken into
    # account when generating the sms completion. Information placed here
    # should be about the sender, not the recipient.
    $pre_prompt = <<<'EOT'
    Here is information about our company:
    - Company Name: John Doe Payment Services, LLC.
    - Services: High Risk Payment Processing
    - Contact: john@johndoe.com, 888-999-1234
    - Calendly: https://calendly.com/johndoe
    EOT;

    # Rules are extra instructions you can give to fine-tune
    # how the AI generates the sms completion.
    $rules = <<<'EOT'
    - Should be no more than 100 characters long.
    EOT;

    # The payload is the information about the recipient.
    $payload = [
        'name' => 'Jane Smith',
        'email' => 'jane@janesmith.com',
        'title' => 'Founder',
        'company' => "Jane's Resume Services",
        'description' => 'Resume writing service.',
    ];

    $response = $api->smsCompletions()->create($pre_prompt, $rules, $payload);

    expect($response->json('trace_id'))->toBeString();
    expect($response->json('info'))->toBeString();
    expect($response->json('completion'))->toBeString();
    expect($response->json('parsed_output'))->toBeArray();
});

it('can create client', function () {

    $api_key = '84cf4ec6-372e-41f0-9a1d-7d4f71ab929e';

    $api = new BiglySalesAi($api_key);

    $response = $api->clients()->create(
        'some-reference-id',
        [
            [
                'name'     => 'file',
                'filename' => 'test-file.txt',
                'path'     => __DIR__ . '/tmp/test-file.txt'
            ]
        ]
    );

})->skip('https://github.com/saloonphp/saloon/issues/324');


it('can list clients', function () {

    $api_key = '84cf4ec6-372e-41f0-9a1d-7d4f71ab929e';

    $bearer_token = ObtainTokenRequest::make('sdk@test.com', 'password')->send()->json('token');

    $api = new BiglySalesAi($api_key, $bearer_token);

    $response = $api->clients()->list();

    expect($response->json('data'))->toBeArray();
});
