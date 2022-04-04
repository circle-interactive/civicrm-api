<?php

use Circle\CiviCRM\AuthenticationTypes;

test('isValidType only returns "true" when a valid type is provided', function () {
    $basic = AuthenticationTypes::BASIC;
    $jwt = AuthenticationTypes::BEARER_JWT;
    $apiKey = AuthenticationTypes::BEARER_API_KEY;

    $basicResult = AuthenticationTypes::isValidType($basic);
    $jwtResult = AuthenticationTypes::isValidType($jwt);
    $apiKeyResult = AuthenticationTypes::isValidType($apiKey);

    expect($basicResult)->toBeTrue();
    expect($jwtResult)->toBeTrue();
    expect($apiKeyResult)->toBeTrue();

    $falseResult = AuthenticationTypes::isValidType('invalid');

    expect($falseResult)->toBeFalse();
});
