<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactorInterface;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformer;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformer;
use ChristianBrown\ApiClient\Transformer\StringToXmlDocTransformer;
use ChristianBrown\ApiClient\Transformer\XmlDocToStringTransformer;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ApiClientContainerFactory implements ApiClientContainerFactoryInterface
{
    public function create(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->register(ApiClientInterface::SERVICE_GUZZLE_CLIENT, Client::class);
        $container->register(ApiClientInterface::SERVICE_GUZZLE_HTTP_FACTORY, HttpFactory::class);

        $container->register(ApiClientInterface::SERVICE_REDACTOR_REQUEST, RequestRedactor::class)
            ->setArguments(
                [
                    RequestRedactorInterface::SENSITIVE_HEADERS,
                    $container->getDefinition(ApiClientInterface::SERVICE_GUZZLE_HTTP_FACTORY),
                ]
            );

        $container->register(ApiClientInterface::SERVICE_REDACTOR_GUZZLE_EXCEPTION, GuzzleExceptionRedactor::class)
            ->setArguments(
                [
                    $container->getDefinition(ApiClientInterface::SERVICE_REDACTOR_REQUEST),
                ]
            );

        $container->register(ApiClientInterface::SERVICE_TRANSFORMER_ARRAY_TO_JSON, ArrayToJsonTransformer::class);
        $container->register(ApiClientInterface::SERVICE_TRANSFORMER_JSON_TO_ARRAY, JsonToArrayTransformer::class);
        $container->register(ApiClientInterface::SERVICE_TRANSFORMER_STRING_TO_XML_DOC, StringToXmlDocTransformer::class);
        $container->register(ApiClientInterface::SERVICE_TRANSFORMER_XML_DOC_TO_STRING, XmlDocToStringTransformer::class);

        $container->register(ApiClientInterface::SERVICE_API_REQUEST_SENDER, ApiRequestSender::class)
            ->setArguments(
                [
                    $container->getDefinition(ApiClientInterface::SERVICE_GUZZLE_CLIENT),
                    $container->getDefinition(ApiClientInterface::SERVICE_REDACTOR_GUZZLE_EXCEPTION),
                ]
            );

        $container->register(ApiClientInterface::SERVICE_JSON_API_REQUEST_SENDER, JsonApiRequestSender::class)
            ->setArguments(
                [
                    $container->getDefinition(ApiClientInterface::SERVICE_API_REQUEST_SENDER),
                    $container->getDefinition(ApiClientInterface::SERVICE_TRANSFORMER_JSON_TO_ARRAY),
                    $container->getDefinition(ApiClientInterface::SERVICE_TRANSFORMER_ARRAY_TO_JSON),
                ]
            );

        $container->register(ApiClientInterface::SERVICE_XML_API_REQUEST_SENDER, XmlApiRequestSender::class)
            ->setArguments(
                [
                    $container->getDefinition(ApiClientInterface::SERVICE_API_REQUEST_SENDER),
                    $container->getDefinition(ApiClientInterface::SERVICE_TRANSFORMER_STRING_TO_XML_DOC),
                    $container->getDefinition(ApiClientInterface::SERVICE_TRANSFORMER_XML_DOC_TO_STRING),
                ]
            );

        return $container;
    }
}
