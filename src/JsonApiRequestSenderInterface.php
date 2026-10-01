<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

/**
 * Every verb in every body encoding. A consumer that needs less should depend on one of the narrower
 * interfaces this one is composed of.
 */
interface JsonApiRequestSenderInterface extends JsonFormApiRequestSenderInterface, JsonMultipartApiRequestSenderInterface, JsonReadApiRequestSenderInterface, JsonWriteApiRequestSenderInterface
{
}
