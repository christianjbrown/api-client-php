<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Exception\Request;

use ChristianBrown\ApiClient\Exception\AbstractException;
use Psr\Http\Message\RequestInterface;
use Throwable;

use function sprintf;

final class TransferException extends AbstractException implements TransferExceptionInterface
{
    public function __construct(RequestInterface $request, ?Throwable $previous = null)
    {
        $message = sprintf(self::MESSAGE, $request->getUri()->__toString());
        parent::__construct($request, $message, $previous);
    }
}
