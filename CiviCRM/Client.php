<?php

declare(strict_types=1);

namespace Circle\CiviCRM;

use Circle\CiviCRM\Http\Requests\CiviRequest;
use RuntimeException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class Client
{
    /**
     * Current version
     *
     * @var string
     */
    public const VERSION = "1.0.0";

    /**
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * @var string
     */
    private string $authenticationType;

    /**
     * @var string
     */
    private string $authenticationKey;

    /**
     * @var array<string, string>
     */
    private array $defaultHeaders = ['X-Requested-With' => 'XMLHttpRequest'];

    /**
     * Class constructor.
     *
     * @param ClientInterface $httpClient
     *  A PSR-18 compliant HTTP client.
     *
     * @param string $authenticationType
     *  The preferred authentication type. This must be one of:
     *   - AuthenticationTypes::BEARER_API_KEY
     *   - AuthenticationTypes::BEARER_JWT
     *   - AuthenticationTypes::BASIC
     *
     * @param string $authenticationKey
     *  The authentication key for the selected authentication type.
     *
     * @param array<string, string> $customHeaders
     *  Custom headers to apply to the request.
     */
    public function __construct(
        ClientInterface $httpClient,
        string $authenticationType,
        string $authenticationKey,
        array $customHeaders = []
    ) {
        $this->httpClient = $httpClient;
        $this->authenticationType = $authenticationType;
        $this->authenticationKey = $authenticationKey;
        $this->defaultHeaders += $customHeaders;

        if (!AuthenticationTypes::isValidType($authenticationType)) {
            throw new RuntimeException("Invalid authentication type {$authenticationType}.");
        }
    }

    /**
     * Execute a "getActions" request to a remote CiviCRM server.
     *
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function getActions(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'getactions', $params);
    }

    /**
     * Execute a "getFields" request to a remote CiviCRM server.
     *
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function getFields(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'getfields', $params);
    }

    /**
     * Execute a "get" request to a remote CiviCRM server.
     *
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function get(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'get', $params);
    }

    /**
     * Execute a "create" request to a remote CiviCRM server.
     *
     * Note: CiviCRM doesn't require parameters to the Contact/create endpoint. As such, executing this request without
     * parameters will create "empty" contact records.
     *
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function create(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'create', $params);
    }

    /**
     * Execute an "update" request to a remote CiviCRM server.
     *
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function update(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'update', $params, true);
    }

    /**
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function save(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'save', $params, true);
    }

    /**
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function delete(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'delete', $params, true);
    }

    /**
     * @param string $entity
     * @param array<mixed> $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function replace(string $entity, array $params = []): ResponseInterface
    {
        return $this->request($entity, 'replace', $params, true);
    }

    /**
     * Perform any arbitrary action on any arbitrary entity, with any arbitrary parameters.
     *
     * This is implemented to allow handling of custom actions, which may not be present across all CiviCRM instances or
     * entities. It follows the civicrm_api3() parameter structure of `entity, action, parameters` which should be
     * familiar to developers with experience of using CiviCRM's API3 in that fashion.
     *
     * @param string $entity
     * @param string $action
     * @param array<mixed> $params
     * @param bool $hasRequiredParams
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    public function request(
        string $entity,
        string $action,
        array $params = [],
        bool $hasRequiredParams = false
    ): ResponseInterface {
        $requestBuilder = new CiviRequest($entity, $action, $params);
        $requestBuilder->setRequiredParams($hasRequiredParams);
        $request = $requestBuilder->build();
        $request = $requestBuilder->withHeaders($request, $this->getHeadersToSend());
        return $this->httpClient->sendRequest($request);
    }

    /**
     * Retrieve headers to add to request.
     *
     * @return string[]
     */
    private function getHeadersToSend(): array
    {
        $basicOrBearer = $this->authenticationType === AuthenticationTypes::BASIC ? 'Basic' : 'Bearer';
        return $this->defaultHeaders += ['X-Civi-Auth' => "{$basicOrBearer} {$this->authenticationKey}"];
    }
}
