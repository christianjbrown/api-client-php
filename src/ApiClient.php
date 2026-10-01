<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

final class ApiClient implements ApiClientInterface
{
    private ApiRequestSenderInterface $apiRequestSender;
    private JsonApiRequestSenderInterface $jsonApiRequestSender;
    private XmlApiRequestSenderInterface $xmlApiRequestSender;

    public function __construct(ApiRequestSenderInterface $apiRequestSender, JsonApiRequestSenderInterface $jsonApiRequestSender, XmlApiRequestSenderInterface $xmlApiRequestSender)
    {
        $this->apiRequestSender = $apiRequestSender;
        $this->jsonApiRequestSender = $jsonApiRequestSender;
        $this->xmlApiRequestSender = $xmlApiRequestSender;
    }

    public function getApiRequestSender(): ApiRequestSenderInterface
    {
        return $this->apiRequestSender;
    }

    public function getJsonApiRequestSender(): JsonApiRequestSenderInterface
    {
        return $this->jsonApiRequestSender;
    }

    public function getXmlApiRequestSender(): XmlApiRequestSenderInterface
    {
        return $this->xmlApiRequestSender;
    }
}
