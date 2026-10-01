<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

/**
 * Every verb in every body encoding. A consumer that needs less should depend on one of the narrower
 * interfaces this one is composed of.
 */
interface ApiRequestSenderInterface extends FormApiRequestSenderInterface, MultipartApiRequestSenderInterface, ReadApiRequestSenderInterface, WriteApiRequestSenderInterface
{
    public const string CONTENT_TYPE_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    public const string CONTENT_TYPE_JSON = 'application/json';
    public const string CONTENT_TYPE_MULTIPART_SPRINTF = 'multipart/form-data; boundary=%s';
    public const string HEADER_AUTHORIZATION = 'Authorization';
    public const string HEADER_CONTENT_TYPE = 'Content-Type';
    public const string HEADER_PROXY_AUTHORIZATION = 'Proxy-Authorization';
    public const string METHOD_DELETE = 'DELETE';
    public const string METHOD_GET = 'GET';
    public const string METHOD_PATCH = 'PATCH';
    public const string METHOD_POST = 'POST';
    public const string METHOD_PUT = 'PUT';
}
