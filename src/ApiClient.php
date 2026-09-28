<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ApiClient implements ApiClientInterface
{
    private ContainerBuilder $container;

    public function __construct(?ApiClientContainerFactoryInterface $containerFactory = null)
    {
        $this->container = ($containerFactory ?? new ApiClientContainerFactory())->create();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getApiRequestSender(): ApiRequestSenderInterface
    {
        /**
         * @var ApiRequestSenderInterface $service
         */
        $service = $this->container->get(self::SERVICE_API_REQUEST_SENDER);

        return $service;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getJsonApiRequestSender(): JsonApiRequestSenderInterface
    {
        /**
         * @var JsonApiRequestSenderInterface $service
         */
        $service = $this->container->get(self::SERVICE_JSON_API_REQUEST_SENDER);

        return $service;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getXmlApiRequestSender(): XmlApiRequestSenderInterface
    {
        /**
         * @var XmlApiRequestSenderInterface $service
         */
        $service = $this->container->get(self::SERVICE_XML_API_REQUEST_SENDER);

        return $service;
    }
}
