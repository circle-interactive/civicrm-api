<?php

namespace Circle\CiviCRM\Http\Requests;

use Circle\CiviCRM\Http\Methods;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

/**
 * Perform an arbitrary action against a specified CiviCRM entity.
 *
 * @internal
 */
class CiviRequest extends AbstractRequestBuilder
{
    /**
     * Class constructor.
     *
     * @param string $entity
     * @param string $action
     * @param array<mixed> $params
     */
    public function __construct(string $entity, string $action, array $params)
    {
        $this->entity = $entity;
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function build(): RequestInterface
    {
        if ($this->hasRequiredParams && empty($this->params)) {
            throw new RuntimeException(self::REQUIRED_PARAMS_EXCEPTION_MESSAGE);
        }

        $method = Methods::POST;
        if (stripos($this->action, 'get') !== false) {
            $method = Methods::GET;
        }

        $url = sprintf(
            "%s/%s/%s",
            AbstractRequestBuilder::BASE_ENDPOINT_URI,
            urlencode($this->entity),
            urlencode($this->action)
        );

        if (!empty($this->params)) {
            $json = json_encode($this->params);
            $url = sprintf("%s?params=%s", $url, urlencode($json));
        }

        return (new Request())
            ->withMethod($method)
            ->withUri(new Uri($url));
    }
}
