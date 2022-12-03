<?php

namespace Chaos\ResponseBuilder;

use Chaos\ResponseBuilder\Exception\InvalidArrayArgumentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseBuilder.
 */
class ResponseBuilder
{
    /**
     * @var bool
     */
    private $status;

    /**
     * @var int $httpStatusCode
     */
    private $httpStatusCode;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $appends = [];

    /**
     * @var array
     */
    private $httpHeaders = [];

    /**
     * @var array
     */
    private $response = [];

    /**
     * @var mixed
     */
    private $data = null;

    /**
     * @var mixed
     */
    private $errors = null;

    /**
     * @var mixed
     */
    private $pagination = null;

    /**
     * @param bool $status
     * @param int $httpStatusCode
     * @param string $message
     */
    public function __construct(bool $status = true, int $httpStatusCode = Response::HTTP_OK, string $message = 'OK')
    {
        $this->status = $status;
        $this->httpStatusCode = $httpStatusCode;
        $this->message = $message;
    }


    /**
     * @param string $message
     * @return $this
     */
    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param array|null $headers
     * @return $this
     */
    public function httpHeaders(array $headers): self
    {
        $this->httpHeaders = $headers;
        return $this;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function httpStatusCode(int $code): self
    {
        $this->httpStatusCode = $code;
        return $this;
    }

    /**
     * @param $resource
     * @param string|null $resourceNamespace
     * @return $this
     */
    private function withData($resource, string $resourceNamespace = null): self
    {
        if (!empty($resourceNamespace)) {
            $data =
                $resource instanceof LengthAwarePaginator ||
                $resource instanceof Collection
                    ? $resourceNamespace::collection($resource)
                    : $resourceNamespace::make($resource);
        } else {
            $data = $resource instanceof LengthAwarePaginator ? $resource->items() : $resource;
        }

        if ($resource instanceof LengthAwarePaginator) {
            $this->pagination = $this->paginationCollection($resource);
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * @param $data
     * @param string|null $resourceNamespace
     * @return $this
     */
    public function success($data, string $resourceNamespace = null): self
    {
        return $this->when(!empty($data), function (ResponseBuilder $builder) use ($data, $resourceNamespace) {
            return $builder->withData($data, $resourceNamespace);
        });
    }

    /**
     * @param $data
     * @return $this
     */
    public function error($data): self
    {
        return (new static(false, Response::HTTP_UNPROCESSABLE_ENTITY, 'Error'))
            ->when(!empty($data), function (ResponseBuilder $builder) use ($data) {
                return $builder->withError($data);
            });
    }

    /**
     * @param $data
     * @return $this
     */
    private function withError($data): self
    {
        $this->errors = $data;
        return $this;
    }

    /**
     * @param array $appends
     * @return $this
     */
    public function append(array $appends = []): self
    {
        if (!empty($appends) && !Arr::isAssoc($appends)) {
            throw new InvalidArrayArgumentException('Appends must be an associative array');
        }
        foreach ($appends as $key => $value) {
            $this->appends[$key] = $value;
        }
        return $this;
    }

    /**
     * @param $resource
     * @return array
     */
    private function paginationCollection($resource): array
    {
        $pagination = $resource->linkCollection()->filter(function ($item) {
            return (int)$item["label"] <= config('response-builder.pagination_size_count');
        })->values();

        return [
            'total'        => $resource->total(),
            'per_page'     => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'last_page'    => $resource->lastPage(),
            'count'        => $resource->count(),
            'from'         => $resource->firstItem(),
            'to'           => $resource->lastItem(),
            'links'        => $pagination,
        ];
    }

    /**
     * @param bool $condition
     * @param callable $callback
     * @return $this
     */
    public function when(bool $condition, callable $callback): self
    {
        if ($condition) {
            return $callback($this);
        }

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function build(): JsonResponse
    {
        $this->response['meta']['status'] = $this->status;
        $this->response['meta']['code'] = $this->httpStatusCode;
        $this->response['meta']['message'] = $this->message;

        if (!empty($this->data)) {
            $this->response['data'] = $this->data;
        }

        if (!empty($this->pagination)) {
            $this->response['pagination'] = $this->pagination;
        }

        if (!empty($this->errors)) {
            $this->response['errors'] = $this->errors;
        }

        if (!empty($this->appends)) {
            $this->response = array_merge($this->response, $this->appends);
        }

        return response()->json($this->response, $this->httpStatusCode, $this->httpHeaders);
    }
}
