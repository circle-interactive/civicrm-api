<?php

namespace Circle\CiviCRM\Http\Requests;

use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
abstract class AbstractRequestBuilder implements RequestBuilderInterface
{
    /**
     * Base endpoint URI for civicrm/ajax/rest to retrieve JSON responses.
     */
    protected const BASE_ENDPOINT_URI = '/civicrm/ajax/api4';

    /**
     * @var string
     *  The entity to perform the relevant action against.
     */
    protected string $entity;

    /**
     * @var string
     *  The action to perform against the relevant entity.
     */
    protected string $action;

    /**
     * @var array<mixed> $params
     *  Params to send to the server.
     */
    protected array $params;

    /**
     * @var bool
     *  Does this Action have required parameters?
     */
    protected bool $hasRequiredParams = false;

    /**
     * @var string
     *  The exception message when required parameters are not present.
     */
    protected const REQUIRED_PARAMS_EXCEPTION_MESSAGE = 'This request requires parameters but none have been provided.';

    /**
     * @param bool $hasRequiredParams
     * @return $this
     */
    public function setRequiredParams(bool $hasRequiredParams): self
    {
        $this->hasRequiredParams = $hasRequiredParams;
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @param array<string, string> $headers
     * @return RequestInterface
     */
    public function withHeaders(RequestInterface $request, array $headers): RequestInterface
    {
        foreach ($headers as $header => $value) {
            $request = $request->withAddedHeader($header, $value);
        }

        return $request;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function build(): RequestInterface;
}
