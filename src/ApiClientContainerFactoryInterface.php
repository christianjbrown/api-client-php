<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ApiClientContainerFactoryInterface
{
    public function create(): ContainerBuilder;
}
