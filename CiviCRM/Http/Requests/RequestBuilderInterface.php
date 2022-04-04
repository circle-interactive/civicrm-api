<?php

namespace Circle\CiviCRM\Http\Requests;

use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
interface RequestBuilderInterface
{
    /**
     * Build a request object.
     *
     * @return RequestInterface
     */
    public function build(): RequestInterface;
}
