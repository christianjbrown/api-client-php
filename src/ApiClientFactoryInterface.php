<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

interface ApiClientFactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(): ApiClientInterface;
}
