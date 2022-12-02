<?php

namespace Chaos\ResponseBuilder\Http\Traits;

use Illuminate\Contracts\Validation\Validator;
use Chaos\ResponseBuilder\Facades\ResponseBuilder;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait FailedValidationTrait
{
    /**
     * @param Validator $validator
     * @return mixed
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): mixed
    {
        $response = ResponseBuilder::error($validator->errors())
            ->httpStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->append(['message' => 'The given data is invalid'])
            ->build();

        throw new ValidationException($validator, $response);
    }
}
