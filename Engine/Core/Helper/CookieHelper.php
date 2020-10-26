<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.12.2018
 * Time: 09:03
 */

namespace Oforge\Engine\Core\Helper;

use DateTime;
use DateTimeImmutable;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CookieHelper
 *
 * @package Oforge\Engine\Core\Helper
 */
class CookieHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * Delete the cookie by putting the expiration date in the past.
     *
     * @param Response $response
     * @param string $cookieName
     *
     * @return Response
     */
    public static function deleteCookie(Response $response, string $cookieName) : Response {
        $cookie = urlencode($cookieName) . '=' . urlencode('deleted')#
                  . '; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/; secure; httponly';

        return $response->withAddedHeader('Set-Cookie', $cookie);
    }

    /**
     * Bake the cookie, but don't eat it!
     *
     * @param Response $response
     * @param string $cookieName
     * @param mixed $cookieValue
     * @param int $expirationMinutes
     *
     * @return Response
     * @throws Exception If an error occurs while creating the expiration date (DateTimeImmutable).
     */
    public static function addCookie(Response $response, string $cookieName, $cookieValue, int $expirationMinutes = 300) : Response {
        if ($expirationMinutes < 0) {
            $expirationMinutes = 0;
        }
        $expiry = new DateTimeImmutable('now + ' . $expirationMinutes . 'minutes');
        $cookie = urlencode($cookieName) . '=' . urlencode($cookieValue)#
                  . '; expires=' . $expiry->format(DateTime::COOKIE)#
                  . '; Max-Age=' . $expirationMinutes#
                  . '; path=/; secure; httponly';

        return $response->withAddedHeader('Set-Cookie', $cookie);
    }

    /**
     * @param Request $request
     * @param string $cookieName
     * @param mixed|null $default Return value if not set yet.
     *
     * @return mixed|null
     */
    public static function getCookieValue(Request $request, string $cookieName, $default = null) {
        $cookies = $request->getCookieParams();

        return isset($cookies[$cookieName]) ? urldecode($cookies[$cookieName]) : $default;
    }

}
