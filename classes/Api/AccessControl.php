<?php

namespace Fias\Api;

use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\Resources;
use \Luracast\Restler\Defaults;

class AccessControl implements iAuthenticate
{
    public static $requires = 'user';
    public static $role = 'user';

    /**
     * @return bool
     */
    public function __isAllowed()
    {
        $roles = [getenv('API_PASSWORD') => 'user'];
        $userClass = Defaults::$userIdentifierClass;
        if (isset($_GET['api_key'])) {
            if (array_key_exists($_GET['api_key'], $roles)) {
                static::$role = $roles[$_GET['api_key']];
                $userClass::setCacheIdentifier(static::$role);
                Resources::$accessControlFunction = 'AccessControl::verifyAccess';

                return true;
            }
        }

        return false;

    }

    /**
     * @return string
     */
    public function __getWWWAuthenticateString()
    {
        return 'Query name="api_key"';
    }

    /**
     * @access private
     *
     * @param array $m
     *
     * @return bool
     */
    public static function verifyAccess(array $m)
    {
        $requires =
            isset($m['class']['AccessControl']['properties']['requires'])
                ? $m['class']['AccessControl']['properties']['requires']
                : false;

        return $requires
            ? static::$role == 'admin' || static::$role == $requires
            : true;
    }
}
