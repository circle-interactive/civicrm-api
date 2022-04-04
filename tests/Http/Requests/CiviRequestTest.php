<?php

use Circle\CiviCRM\Http\Requests\CiviRequest;

test('"get" methods are sent with the "GET" HTTP method', function () {
    $entity = 'Contact';

    $getAction = 'get';
    $getFieldsAction = 'getFields';

    $params = [];

    $getRequestBuilder = new CiviRequest($entity, $getAction, $params);
    $getRequestBuilder->setRequiredParams(false);
    $getRequest = $getRequestBuilder->build();

    expect($getRequest->getMethod())->toBe('GET');

    $getFieldsRequestBuilder = new CiviRequest($entity, $getFieldsAction, $params);
    $getFieldsRequestBuilder->setRequiredParams(false);
    $getFieldsRequest = $getRequestBuilder->build();

    expect($getFieldsRequest->getMethod())->toBe('GET');
});

test('"non-get" methods are sent with the "POST" HTTP method', function () {
    $entity = 'Contact';

    // TODO: test the rest of the actions
    $createAction = 'create';
    $deleteAction = 'delete';

    $params = [];

    $createRequestBuilder = new CiviRequest($entity, $createAction, $params);
    $createRequestBuilder->setRequiredParams(false);
    $createRequest = $createRequestBuilder->build();

    expect($createRequest->getMethod())->toBe('POST');

    $deleteRequestBuilder = new CiviRequest($entity, $deleteAction, $params);
    $deleteRequestBuilder->setRequiredParams(false);
    $deleteRequest = $deleteRequestBuilder->build();

    expect($deleteRequest->getMethod())->toBe('POST');
});

test('an exception is thrown when an entity has required parameters but none are passed', function () {
    $entity = 'Contact';
    $action = 'update';
    $params = [];

    $throwingCreateRequestBuilder = new CiviRequest($entity, $action, $params);
    $throwingCreateRequestBuilder->setRequiredParams(true);
    expect(fn() => $throwingCreateRequestBuilder->build())->toThrow(RuntimeException::class);

    $params = ['id' => 1];

    $successfulCreateRequestBuilder = new CiviRequest($entity, $action, $params);
    $successfulCreateRequestBuilder->setRequiredParams(true);
    expect(fn() => $successfulCreateRequestBuilder->build())->not->toThrow(RuntimeException::class);
});

test('the URI is correct when the request does not have parameters', function () {
    $entity = 'Contact';
    $action = 'get';
    $params = [];

    $requestBuilder = new CiviRequest($entity, $action, $params);
    $requestBuilder->setRequiredParams(false);
    $request = $requestBuilder->build();

    $uri = $request->getUri();
    $path = $uri->getPath();

    expect($path)->toBe('/civicrm/ajax/api4/Contact/get');
});

test('the URI is correct when the request has parameters', function () {
    $entity = 'Activity';
    $action = 'create';
    $params = ['activity_type_id' => 1, 'subject' => 'Test Activity',];

    $requestBuilder = new CiviRequest($entity, $action, $params);
    $requestBuilder->setRequiredParams(true);
    $request = $requestBuilder->build();

    $uri = $request->getUri();
    $path = $uri->getPath();

    $actualQueryString = $uri->getQuery();
    $expectedQueryString = 'params=%7B%22values%22%3A%7B%22activity_type_id%22%3A1%2C%22subject%22%3A%22Test+Activity%22%7D%7D'; // This is URL encoded JSON

    expect($path)->toBe('/civicrm/ajax/api4/Activity/create');
    expect($actualQueryString)->toBe($expectedQueryString);
});
