<?php
/**
 * Проверка доступа к методу
 * Варианты @requires: authorized, manager, admin
 */
namespace Fias\Api;

use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\Resources;
use Luracast\Restler\RestException;

class AccessControl implements iAuthenticate
{
    public static $requires = '';
    public static $role = [];
    public static $managerGroup = [4, 5, 6, 10, 12, 15];

    /**
     * Проверяет доступ к апи по группе пользователя
     * Метод вызывается при прямом обращении к апи
     */
    public function __isAllowed()
    {
        /** @globals \CUser $USER */
        global $USER;

        // Для автодокументации мы указываем метод, который будет проверять права доступа по группам
        Resources::$accessControlFunction = '\Api\AccessControl::verifyAccess';

        // Place Yandex-cpa token here
        if (
            property_exists($this, 'restler') &&
            explode('/', $this->restler->url)[0] === 'yandex'
        ) {
            if ($_SERVER['HTTP_AUTHORIZATION'] === '4100000171574B90') {
                static::$role[] = 'yandex-cpa';
            } else {
                throw new RestException(403, 'Forbidden');
            }
        }

        // Получаем группы пользователя
        if ($USER->IsAuthorized()) {
            static::$role = $USER->GetUserGroup($USER->GetID());
            static::$role[] = 'authorized';
            if (array_intersect(static::$role, static::$managerGroup)) {
                static::$role[] = 'manager';
                return true;
            }
        }

        // Если админ, то можно всё
        if ($USER->IsAdmin()) {
            static::$role[] = 'admin';

            return true;
        }

        // Если требуется проверка по группе пользователя
        if (static::$requires) {
            return in_array(static::$requires, static::$role);
        } else {
            return true;
        }
    }

    /**
     * Проверяет доступ к апи по группе пользователя
     * Метод вызывается автодокументацией после вызова __isAllowed
     *
     * @access private
     */
    public static function verifyAccess(array $m)
    {
        $requires =
            isset($m['class']['\\Api\\AccessControl']['properties']['requires'])
                ? $m['class']['\\Api\\AccessControl']['properties']['requires']
                : false;

        return $requires
            ? in_array('admin', static::$role) || in_array($requires, static::$role)
            : true;
    }

    /**
     * @return string string to be used with WWW-Authenticate header
     * @example Basic
     * @example Digest
     * @example OAuth
     */
    public function __getWWWAuthenticateString()
    {
        return null;
    }
}
