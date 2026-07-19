<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Exception\Parse\ParseXmlExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use ChristianBrown\ApiClient\Transformer\StringToXmlDocTransformerInterface;
use ChristianBrown\ApiClient\Transformer\XmlDocToStringTransformerInterface;
use DOMDocument;

final class XmlApiRequestSender implements XmlApiRequestSenderInterface
{
    private ApiRequestSenderInterface $apiRequestSender;
    private XmlDocToStringTransformerInterface $requestTransformer;
    private StringToXmlDocTransformerInterface $responseTransformer;

    public function __construct(ApiRequestSenderInterface $apiRequestSender, StringToXmlDocTransformerInterface $responseTransformer, XmlDocToStringTransformerInterface $requestTransformer)
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
     * @throws ParseXmlExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function get(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): DOMDocument
    {
        $contents = $this->apiRequestSender->get($requestUrl, $requestQueryStrings, $requestHeaders);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_GET, $requestUrl, $requestQueryStrings));
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param null|DOMDocument      $requestDomDocument  The request body document
     *
     * @throws ConnectExceptionInterface
     * @throws ParseXmlExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function post(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?DOMDocument $requestDomDocument = null): DOMDocument
    {
        $requestBodyString = null;
        if ($requestDomDocument instanceof DOMDocument) {
            $requestBodyString = $this->requestTransformer->transform($requestDomDocument);
        }
        $contents = $this->apiRequestSender->post($requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyString);

        return $this->responseTransformer->transform($contents, new RequestContext(ApiRequestSenderInterface::METHOD_POST, $requestUrl, $requestQueryStrings));
    }
}
