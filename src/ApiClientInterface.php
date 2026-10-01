<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

interface ApiClientInterface
{
    public const string SERVICE_API_REQUEST_SENDER = 'christianbrown.api_client.api_request_sender';
    public const string SERVICE_GUZZLE_CLIENT = 'guzzle_http.client';
    public const string SERVICE_GUZZLE_HTTP_FACTORY = 'guzzle_http.http_factory';
    public const string SERVICE_JSON_API_REQUEST_SENDER = 'christianbrown.api_client.json_api_request_sender';
    public const string SERVICE_MULTIPART_BODY_FACTORY = 'christianbrown.api_client.multipart.multipart_body_factory';
    public const string SERVICE_REDACTOR_GUZZLE_EXCEPTION = 'christianbrown.api_client.redactor.guzzle_exception_redactor';
    public const string SERVICE_REDACTOR_REQUEST = 'christianbrown.api_client.redactor.request_redactor';
    public const string SERVICE_TRANSFORMER_ARRAY_TO_JSON = 'christianbrown.api_client.transformer.array_to_json_transformer';
    public const string SERVICE_TRANSFORMER_JSON_TO_ARRAY = 'christianbrown.api_client.transformer.json_to_array_transformer';
    public const string SERVICE_TRANSFORMER_STRING_TO_XML_DOC = 'christianbrown.api_client.transformer.string_to_xml_doc_transformer';
    public const string SERVICE_TRANSFORMER_XML_DOC_TO_STRING = 'christianbrown.api_client.transformer.xml_doc_to_string_transformer';
    public const string SERVICE_XML_API_REQUEST_SENDER = 'christianbrown.api_client.xml_api_request_sender';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getApiRequestSender(): ApiRequestSenderInterface;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getJsonApiRequestSender(): JsonApiRequestSenderInterface;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getXmlApiRequestSender(): XmlApiRequestSenderInterface;
}
