<?php

use Illuminate\Support\Facades\Validator;
use Chaos\ResponseBuilder\Facades\ResponseBuilder;
use Chaos\ResponseBuilder\Resources\MessageResource;
use Illuminate\Validation\Rule;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    // When testing inside of a Laravel installation, this is not needed
    protected function getPackageProviders($app): array
    {
        return [
            'Chaos\ResponseBuilder\PackageServiceProvider'
        ];
    }

    /**
     * @var array
     */
    private array $data = [
        'device_id'     => "26728172-d050-4126-8ee2-4bfe8201565c",
        'secret'        => "0184cd97-7351-7121-91cb-5a818f3eb4b0",
        'platform'      => "iOS",
        'version'       => "1.0",
        "language_code" => "en",
        "country_code"  => "TR",
        "time_zone"     => "Europe/Istanbul",
    ];

    /**
     * @var string
     */
    private string $customKey = 'custom-key';

    /** @test */
    public function test_data_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->data)->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["data"];

        $this->assertTrue($this->data === $responseData);
    }

    /** @test */
    public function test_message_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->data)->message('test')->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["meta"]["message"];

        $this->assertTrue(!is_null($responseData));
    }

    /** @test */
    public function test_append_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->data)->append([$this->customKey => 'value'])->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)[$this->customKey];

        $this->assertTrue(!is_null($responseData));
    }

    /** @test */
    public function test_http_status_code_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->data)->httpStatusCode(Response::HTTP_OK)->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["meta"]["code"];

        $this->assertTrue(Response::HTTP_OK === $responseData);
    }

    /** @test */
    public function test_with_resource_data_should_return_success_response()
    {
        $data = collect(
            [
                "tr" => "Merhaba! Aklındaki tüm soruları sorabilirsin. En kısa sürede cevaplayacağım. 🤗",
                "en" => "Hello! You can ask all your questions. I'll answer them as soon as I can. 🤗",
                "ru" => "Здравствуйте! Вы можете задать все интересующие Вас вопросы. Я отвечу на них в ближайшее время. 🤗",
                "uk" => "Вітаю! Ви можете задати всі ваші запитання. Я відповім на них якомога швидше. 🤗",
                "es" => "¡Hola! Puedes hacer todas tus preguntas. Las responderé tan pronto como pueda 🤗",
                "de" => "Hallo! Du kannst alle deine Fragen stellen. Ich werde sie so schnell wie möglich beantworten. 🤗",
                "he" => "שלום! אתה יכול לשאול את כל השאלות שלך. אענה להם ברגע שאוכל 🤗",
                "ar" => "مرحبًا! يمكنك أن تسأل كل أسئلتك. سأجيب عليهم بأسرع ما يمكن 🤗",
                "pt" => "Olá! Você pode tirar todas as suas dúvidas. Vou respondê-las assim que puder 🤗",
                "ja" => "こんにちは！いかなる質問でもお聞きください。早急に回答いたします。 🤗",
            ]
        );

        $response = ResponseBuilder::success($data, MessageResource::class)->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["data"];

        $this->assertTrue(!is_null($responseData));
    }

    /** @test */
    public function test_validation_should_return_error_response()
    {
        $validator = Validator::make($this->data, [
            'device_id' => 'required|size:12', // device length should be 12 chars
            'secret'    => 'required',
            'platform'  => ['required', Rule::in('Android', 'iOS', 'Huawei')],
        ]);
        $response = ResponseBuilder::error($validator->errors())->httpStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)['errors'];

        $this->assertTrue(!is_null($responseData));
    }
}
