# civicrm-api

civicrm-api is a Composer package that allows developers to interact with a [CiviCRM](https://civicrm.org) instance 
using the REST API v4 functionality within CiviCRM.

This code is intended to be used outside of a CiviCRM deployment environment. As such, the codebase adheres to PSR-12
coding standards instead of Drupal/Wordpress/CiviCRM coding standards.

## Installation

To install the library, run the following command:

`composer require circle-interactive/civicrm-api`

## Requirements

1. The CiviCRM server **must** be running on v5.36 or later. 

2. Your application **must** use an HTTP client that is [PSR-18](https://www.php-fig.org/psr/psr-18/) compliant.

If your application uses an HTTP client that is not compliant with [PSR-18](https://www.php-fig.org/psr/psr-18/), it 
will be necessary to write a decorator class that implements a `sendRequest` method.

## How to Use

_NB: As you can see from the examples, this library does not deserialize responses. This is by design._

_Calling code is responsible for response deserialization and handling the data from that response._

### Guzzle Example

```php
<?php

declare(strict_types=1);

// Require Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Define the desired type of authentication and key
$authType = \Circle\CiviCRM\AuthenticationTypes::BEARER_API_KEY;
$authKey = 'MYKEYGOESHERE'; // best practices dictate using an environment variable here!

// Configure Guzzle to point at the root URL for the CiviCRM site
// note we DO NOT include the /civicrm section of the URI in the base_uri field
$guzzleOptions = ['base_uri' => 'https://my.civicrm.site']; 
// Instantiate a new Guzzle client
$guzzleClient = new GuzzleHttp\Client($guzzleOptions);

// Instantiate a new CiviCRM API client, passing the Guzzle client as a parameter
$civicrm = new \Circle\CiviCRM\Client($guzzleClient, $authType, $authKey);

// Make request to retrieve all Contacts
$contactsResponse = $civicrm->get('Contact');
// Note that the full API response object is returned to the caller
$contactsJson = $contactsResponse->getBody()->getContents();
// Decode the response
$contacts = json_decode($contactsJson, TRUE);
$contactsArray = $contacts['values'];

var_dump($contactsArray);
```

### Symfony HTTP Client Example

```php
<?php

declare(strict_types=1);

// Require Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Define the desired type of authentication and key
$authType = \Circle\CiviCRM\AuthenticationTypes::BEARER_API_KEY;
$authKey = 'MYKEYGOESHERE'; // best practices dictate using an environment variable here!

// Instantiate a new Symfony HTTP client
$symfonyClient = \Symfony\Component\HttpClient\HttpClient::createForBaseUri('https://my.civicrm.site');
$psr18client = new \Symfony\Component\HttpClient\Psr18Client($symfonyClient);

// Instantiate a new CiviCRM API client, passing the PSR18-compatible Symfony HTTP client as a parameter
$civicrm = new \Circle\CiviCRM\Client($psr18client, $authType, $authKey);

// Make request to retrieve all Activities
$activityResponse = $civicrm->get('Activity');
// Note that the full API response object is returned to the caller
$activityJson = $activityResponse->getBody()->getContents();
// Decode the response
$activities = json_decode($activityJson, TRUE);
$activitiesArray = $activities['values'];

var_dump($activitiesArray);
```

_NB: Use correct, case-specific Entity names when interacting with the API (for example: "activity" will return an error,
use "Activity" instead)_

## Available Functions

The examples make use of the `get` method exposed by this library. However, this is not the only available method.
The list of available methods is as follows:

1. getActions
2. getFields
3. get
4. create
5. update
6. save
7. delete
8. replace

As well as the above, the library also exposes the `request` method. This allows for arbitrary combinations of `Entity`, 
`action`, and `params`. An example of usage of this method can be seen below:

```php
$civicrm = new \Circle\CiviCRM\Client($psr18client, $authType, $authKey);
$myCustomResponse = $civicrm->request('CustomEntity', 'customaction', ['my' => 'custom', 'params']);
```

This would be useful in the following (non-exhaustive list of) situations: 
- Your CiviCRM instance contains custom entities
- You want to perform an entity-specific action (for example: `MailSettings.testConnection`, or `Setting.set`)

## Available Authentication Methods

This library uses the `X-Civi-Auth` Header when making requests to CiviCRM instances. As such, the following authentication
methods are available:

| Type of Authentication | Library Mapping                                    |
|------------------------|----------------------------------------------------|
 | Basic Authentication   | Circle\CiviCRM\AuthenticationTypes::BASIC          |
 | API Key Authentication | Circle\CiviCRM\AuthenticationTypes::BEARER_API_KEY |
| JWT Authentication     | Circle\CiviCRM\AuthenticationTypes::BEARER_JWT     |

Any other form of authentication is unsupported by this library.

_NB: When creating a Client object, only pass the Authentication Key (ie: `MYKEY`)._

_Do not pass the Bearer or Basic keywords (ie: `Bearer MYKEY` or `Basic MYKEY`)as these are put in place by the library._

### Note on Basic Authentication
When using Basic Authentication, the key is a base64 encoded string: `B64(USER:PASSWORD)`. You will need to provide the 
Base64 encoded string as the `$authKey` parameter to the `Circle\CiviCRM\Client` constructor.

## Contributing

If you find a bug or other issue within this library, please raise an issue in this Github repository.

To run tests, run `composer test`. The library uses Pest for automated testing, and adheres to PHPStan Level 6.

## Resources

- [CiviCRM APIv4 documentation](https://docs.civicrm.org/dev/en/latest/api/v4/usage/)
- [CiviCRM AuthX documentation](https://docs.civicrm.org/dev/en/latest/framework/authx/)
- [Pest](https://pestphp.com/)
- [PHPStan](https://phpstan.org/)

## Support

The technical team at [Circle Interactive](https://www.circle-interactive.co.uk) are responsible for the maintenance of this
library. Circle offer CiviCRM implementations for third-sector clients of any kind, no matter the size.

## License

This package is licensed with [AGPL-3.0](https://www.gnu.org/licenses/agpl-3.0.en.html), as is CiviCRM. 
