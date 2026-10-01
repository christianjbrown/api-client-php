<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\TransferExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use ChristianBrown\ApiClient\Multipart\MultipartPartInterface;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformerInterface;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformerInterface;

use Closure;

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
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function delete(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_DELETE, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->delete($requestUrl, $requestQueryStrings, $requestHeaders));
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function get(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_GET, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->get($requestUrl, $requestQueryStrings, $requestHeaders));
    }

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patch(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array
    {
        return $this->respond(
            ApiRequestSenderInterface::METHOD_PATCH,
            $requestUrl,
            $requestQueryStrings,
            fn (RequestContextInterface $context): string => $this->apiRequestSender->patch($requestUrl, $requestQueryStrings, self::toJsonRequestHeaders($requestHeaders), $this->toJsonRequestBody($requestBodyArray, $context)),
        );
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patchForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_PATCH, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->patchForm($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData));
    }

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patchMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_PATCH, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->patchMultipart($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyParts));
    }

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function post(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array
    {
        return $this->respond(
            ApiRequestSenderInterface::METHOD_POST,
            $requestUrl,
            $requestQueryStrings,
            fn (RequestContextInterface $context): string => $this->apiRequestSender->post($requestUrl, $requestQueryStrings, self::toJsonRequestHeaders($requestHeaders), $this->toJsonRequestBody($requestBodyArray, $context)),
        );
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function postForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_POST, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->postForm($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData));
    }

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function postMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_POST, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->postMultipart($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyParts));
    }

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function put(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array
    {
        return $this->respond(
            ApiRequestSenderInterface::METHOD_PUT,
            $requestUrl,
            $requestQueryStrings,
            fn (RequestContextInterface $context): string => $this->apiRequestSender->put($requestUrl, $requestQueryStrings, self::toJsonRequestHeaders($requestHeaders), $this->toJsonRequestBody($requestBodyArray, $context)),
        );
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function putForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_PUT, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->putForm($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData));
    }

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function putMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): array
    {
        return $this->respond(ApiRequestSenderInterface::METHOD_PUT, $requestUrl, $requestQueryStrings, fn (): string => $this->apiRequestSender->putMultipart($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyParts));
    }

    /**
     * The one path every verb goes through: builds the request context, sends the request through the
     * closure, and decodes the response against that context.
     *
     * @param string                                   $method              The HTTP method used for the request
     * @param string                                   $requestUrl          The request URL
     * @param array<string, string>                    $requestQueryStrings
     * @param Closure(RequestContextInterface): string $send                Sends the request and returns the raw response body
     *
     * @throws ParseJsonExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    private function respond(string $method, string $requestUrl, array $requestQueryStrings, Closure $send): array
    {
        $context = new RequestContext($method, $requestUrl, $requestQueryStrings);

        return $this->responseTransformer->transform($send($context), $context);
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
