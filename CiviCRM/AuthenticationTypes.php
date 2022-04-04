<?php

namespace Circle\CiviCRM;

/**
 * Contains logic to determine whether an authentication type is considered "valid" by the civicrm-api library.
 */
final class AuthenticationTypes
{
    public const BEARER_API_KEY = 'bearer_api_key';
    public const BEARER_JWT = 'bearer_jwt';
    public const BASIC = 'basic';

    /**
    * @param string $type
    * @return bool
    */
    public static function isValidType(string $type): bool
    {
        switch ($type) {
            case self::BASIC:
            case self::BEARER_API_KEY:
            case self::BEARER_JWT:
                return true;

            default:
                return false;
        }
    }
}
