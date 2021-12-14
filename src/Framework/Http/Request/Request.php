<?php

namespace Framework\Http\Request;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

class Request implements ServerRequestInterface
{
    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $cookieParams = [];

    /**
     * @var null|array|object
     */
    private $parsedBody;

    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @var array
     */
    private $serverParams;

    /**
     * @var array
     */
    private $uploadedFiles;

    /**
     * List of all registered headers, as key => array of values.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Map of normalized header name to original name used to register header.
     *
     * @var array
     */
    protected $headerNames = [];

    /**
     * @var string
     */
    private $protocol = '1.1';

    /**
     * @var StreamInterface
     */
    private $stream;

    public function __construct(
        array $serverParams = [],
        array $uploadedFiles = [],
        $body = 'php://input',
        array $headers = [],
        array $cookies = [],
        array $queryParams = [],
        $parsedBody = null,
        $protocol = '1.1'
    )
    {
        $this->serverParams = $serverParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->cookieParams = $cookies;
        $this->queryParams = $queryParams;
        $this->parsedBody = $parsedBody;
        $this->protocol = $protocol;
    }

    public function getUriPath()
    {
        return $this->serverParams['REQUEST_URI'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        $this->validateProtocolVersion($version);
        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        if (! $this->hasHeader($name)) {
            return [];
        }

        $header = $this->headerNames[strtolower($name)];

        return $this->headers[$header];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        $value = $this->getHeader($name);

        if (empty($value)) {
            return '';
        }

        return implode(',', $value);
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $normalized = strtolower($name);

        $new = clone $this;
        if ($new->hasHeader($name)) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }

        $new->headerNames[$normalized] = $name;
        $new->headers[$name] = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        // TODO: Implement getBody() method.
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        if (empty($this->method)) {
            return 'GET';
        }

        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    /**
     * @inheritDoc
     */
    public function getUri()
    {
        // TODO: Implement getUri() method.
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }

    /**
     * @inheritDoc
     */
    public function getServerParams()
    {
        // TODO: Implement getServerParams() method.
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams()
    {
        // TODO: Implement getQueryParams() method.
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles()
    {
        // TODO: Implement getUploadedFiles() method.
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody()
    {
        // TODO: Implement getParsedBody() method.
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data)
    {
        // TODO: Implement withParsedBody() method.
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name, $default = null)
    {
        if (! array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * @inheritDoc
     */
    public function withAttribute($name, $value)
    {
        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }

    /**
     * Validate the HTTP protocol version
     *
     * @param string $version
     * @throws InvalidArgumentException on invalid HTTP protocol version
     */
    private function validateProtocolVersion($version)
    {
        if (empty($version)) {
            throw new InvalidArgumentException(
                'HTTP protocol version can not be empty'
            );
        }
        if (! is_string($version)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP protocol version; must be a string, received %s',
                (is_object($version) ? get_class($version) : gettype($version))
            ));
        }

        // HTTP/1 uses a "<major>.<minor>" numbering scheme to indicate
        // versions of the protocol, while HTTP/2 does not.
        if (! preg_match('#^(1\.[01]|2)$#', $version)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP protocol version "%s" provided',
                $version
            ));
        }
    }
}