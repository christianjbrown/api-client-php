<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformerInterface;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformerInterface;

use function array_merge;

final class JsonApiRequestSender implements JsonApiRequestSenderInterface
{
    private ApiRequestSenderInterface $apiRequestSender;
    private ArrayToJsonTransformerInterface $requestTransformer;
    private JsonToArrayTransformerInterface $responseTransformer;

    public function __construct(ApiRequestSenderInterface $apiRequestSender, JsonToArrayTransformerInterface $responseTransformer, ArrayToJsonTransformerInterface $requestTransformer)
    {
        $this->apiRequestSender = $apiRequestSender;
        $this->responseTransformer = $responseTransformer;
        $this->requestTransformer = $requestTransformer;
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function delete(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): array
    {
        $contents = $this->apiRequestSender->delete($requestUrl, $requestQueryStrings, $requestHeaders);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_DELETE, $requestUrl, $requestQueryStrings));
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function get(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): array
    {
        $contents = $this->apiRequestSender->get($requestUrl, $requestQueryStrings, $requestHeaders);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_GET, $requestUrl, $requestQueryStrings));
    }

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patch(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array
    {
        $context = new RequestContext(ApiRequestSenderInterface::METHOD_PATCH, $requestUrl, $requestQueryStrings);
        $contents = $this->apiRequestSender->patch($requestUrl, $requestQueryStrings, self::toJsonRequestHeaders($requestHeaders), $this->toJsonRequestBody($requestBodyArray, $context));

        return $this->responseTransformer->transform($contents, $context);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patchForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array
    {
        $contents = $this->apiRequestSender->patchForm($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_PATCH, $requestUrl, $requestQueryStrings));
    }

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function post(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array
    {
        $context = new RequestContext(ApiRequestSenderInterface::METHOD_POST, $requestUrl, $requestQueryStrings);
        $contents = $this->apiRequestSender->post($requestUrl, $requestQueryStrings, self::toJsonRequestHeaders($requestHeaders), $this->toJsonRequestBody($requestBodyArray, $context));

        return $this->responseTransformer->transform($contents, $context);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function postForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array
    {
        $contents = $this->apiRequestSender->postForm($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_POST, $requestUrl, $requestQueryStrings));
    }

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function put(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array
    {
        $context = new RequestContext(ApiRequestSenderInterface::METHOD_PUT, $requestUrl, $requestQueryStrings);
        $contents = $this->apiRequestSender->put($requestUrl, $requestQueryStrings, self::toJsonRequestHeaders($requestHeaders), $this->toJsonRequestBody($requestBodyArray, $context));

        return $this->responseTransformer->transform($contents, $context);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function putForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array
    {
        $contents = $this->apiRequestSender->putForm($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_PUT, $requestUrl, $requestQueryStrings));
    }

    /**
     * Encodes a request body as JSON, or leaves it absent when the caller supplied none.
     *
     * @param null|array<array-key, mixed> $requestBodyArray
     * @param RequestContextInterface      $context          The context the encoder reports failures against
     *
     * @throws ParseJsonExceptionInterface
     */
    private function toJsonRequestBody(?array $requestBodyArray, RequestContextInterface $context): ?string
    {
        if (null === $requestBodyArray) {
            return null;
        }

        return $this->requestTransformer->transform($requestBodyArray, $context);
    }

    /**
     * Defaults the JSON content type, but lets a caller-supplied header win.
     *
     * @param array<string, string> $requestHeaders
     *
     * @return array<string, string>
     */
    private static function toJsonRequestHeaders(array $requestHeaders): array
    {
        return array_merge([ApiRequestSenderInterface::HEADER_CONTENT_TYPE => ApiRequestSenderInterface::CONTENT_TYPE_JSON], $requestHeaders);
    }
}
