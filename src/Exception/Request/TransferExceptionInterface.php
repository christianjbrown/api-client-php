<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Exception\Request;

/**
 * The request failed in transit for a reason other than failing to connect: the connection dropped
 * mid-response, the TLS certificate was rejected, or the transport gave up in some other way before a
 * usable response arrived.
 */
interface TransferExceptionInterface extends RequestExceptionInterface
{
    public const string MESSAGE = 'The request to %s failed in transit';
}
