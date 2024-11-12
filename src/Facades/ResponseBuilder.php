<?php

namespace IbrahimHalilUcan\ResponseBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static ResponseBuilder message(string $message)
 * @method static ResponseBuilder httpHeaders(array $headers)
 * @method static ResponseBuilder append(array $appends = []) .
 * @method static ResponseBuilder success($data, string|null $resourceNamespace = null)
 * @method static ResponseBuilder error($data)
 * @method static ResponseBuilder noContent()
 * @method static JsonResponse build()
 *
 * @see \IbrahimHalilUcan\ResponseBuilder\ResponseBuilder
 */
class ResponseBuilder extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'response_builder';
    }
}
