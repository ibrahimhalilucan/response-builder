<?php

use Faker\Factory;
use Illuminate\Support\Facades\Validator;
use IbrahimHalilUcan\ResponseBuilder\Facades\ResponseBuilder;
use Illuminate\Validation\Rule;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    private $item;

    protected $faker;

    /**
     * @var string
     */
    private $customKey = 'custom-key';

    public function setUp(): void
    {
        parent::setUp();
        $faker = Factory::create('en_US');
        $data = [
            'device_id'     => $faker->uuid,
            'secret'        => $faker->unixTime,
            'agent'         => $faker->iosMobileToken,
            'version'       => $faker->semver(),
            "language_code" => $faker->languageCode,
            "country_code"  => $faker->countryCode,
            "time_zone"     => $faker->timezone,
        ];

        $this->item = $data;
    }

    // When testing inside of a Laravel installation, this is not needed
    protected function getPackageProviders($app): array
    {
        return [
            'IbrahimHalilUcan\ResponseBuilder\PackageServiceProvider'
        ];
    }

    /** @test */
    public function test_data_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->item)->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["data"];

        $this->assertTrue($this->item === $responseData);
    }

    /** @test */
    public function test_message_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->item)->message('test')->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["meta"]["message"];

        $this->assertTrue(!is_null($responseData));
    }

    /** @test */
    public function test_append_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->item)->append([$this->customKey => 'value'])->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)[$this->customKey];

        $this->assertTrue(!is_null($responseData));
    }

    /** @test */
    public function test_http_status_code_should_return_success_response()
    {
        $response = ResponseBuilder::success($this->item)->httpStatusCode(Response::HTTP_OK)->build();

        $response = $response->getContent();

        $responseData = json_decode($response, true)["meta"]["code"];

        $this->assertTrue(Response::HTTP_OK === $responseData);
    }

    /** @test */
    public function test_validation_should_return_error_response()
    {
        $validator = Validator::make($this->item, [
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
