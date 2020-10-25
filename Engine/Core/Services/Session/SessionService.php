<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 12:29
 */

namespace Oforge\Engine\Core\Services\Session;

/**
 * Service to create secure sessions
 * Class SessionManagementService
 */
class SessionService {

    /**
     * Start the session
     *
     * @param int $lifetimeSeconds
     * @param string $path
     * @param string|null $domain
     * @param bool|null $secure
     */
    public function start($lifetimeSeconds = 0, string $path = '/', ?string $domain = null, ?bool $secure = null) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_name('oforge_session');
            if (isset($_SESSION['session_deleted']) && !empty($_SESSION['session_deleted'])
                && $_SESSION['session_deleted'] < time() - 180) {
                session_destroy();
            }
            // Set the domain to default to the current domain.
            $domain = empty($domain) ? $_SERVER['SERVER_NAME'] : $domain;
            // Set the default secure value to whether the site is being accessed with SSL
            $secure = $secure ?? (isset($_SERVER['HTTPS']) ? true : false);
            // Set the cookie settings and start the session
            session_set_cookie_params($lifetimeSeconds, $path, $domain, $secure, true);
            session_start();
            $_SESSION['session_created'] = time();
        }
    }

    /**
     * Refresh a session
     */
    public function refresh() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $oldSessionData = $_SESSION;
        $this->sessionDestroy();
        $this->start(0);
        $_SESSION = array_merge($_SESSION, $oldSessionData);

        $_SESSION['session_created'] = time();
    }

    /**
     * Destroy the session an the corresponding cookie
     */
    public function sessionDestroy() {
        // $oldCookieParams = session_get_cookie_params();
        // setcookie(session_name(), '', time() - 3600, $oldCookieParams['path'], $oldCookieParams['domain'], $oldCookieParams['secure'], isset($oldCookieParams['httponly']));
        // TODO destroyen des alten cookies testen

        $_SESSION = [];
        session_destroy();
        session_id(session_create_id());
    }

}
