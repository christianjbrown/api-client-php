<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

/**
 * The composition root: builds the object graph for the given options and hands the senders to an `ApiClient`.
 */
final class ApiClientFactory implements ApiClientFactoryInterface
{
    private ClientOptionsInterface $clientOptions;

    public function __construct(ClientOptionsInterface $clientOptions)
    {
        $this->clientOptions = $clientOptions;
    }

    public function create(): ApiClientInterface
    {
        $container = (new ApiClientContainerFactory($this->clientOptions))->create();

        /**
 * @var ApiRequestSenderInterface $apiRequestSender
*/
        $apiRequestSender = $container->get(ApiClientInterface::SERVICE_API_REQUEST_SENDER);
        /**
 * @var JsonApiRequestSenderInterface $jsonApiRequestSender
*/
        $jsonApiRequestSender = $container->get(ApiClientInterface::SERVICE_JSON_API_REQUEST_SENDER);
        /**
 * @var XmlApiRequestSenderInterface $xmlApiRequestSender
*/
        $xmlApiRequestSender = $container->get(ApiClientInterface::SERVICE_XML_API_REQUEST_SENDER);

        return new ApiClient($apiRequestSender, $jsonApiRequestSender, $xmlApiRequestSender);
    }
}
