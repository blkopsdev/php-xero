<?php
/**
 * @package    xero-php-sample-app
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace App\Helper;

/**
 * This is a very basic helper for session persistence.
 * In reality, this would likely use a different persistence layer
 *
 * Class XeroSessionStorage
 * @package App\Helper
 */
class XeroSessionStorage
{

    const SESSION_KEY = 'xero_oauth';

    public function __construct()
    {
        session_start();
    }


    /**
     * @return null|\ArrayObject
     */
    public function getSession()
    {
        //If it doesn't exist, return null
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return null;
        }

        $session = $_SESSION[self::SESSION_KEY];

        //If it's expired, return null
        if ($session->expires !== null && $session->expires <= time()) {
            return null;
        }

        return $session;
    }


    /**
     * @param $token
     * @param $secret
     * @param null $expires
     */
    public function setSession($token, $secret, $expires = null)
    {
        // expires sends back an int
        if ($expires !== null) {
            $expires = time() + intval($expires);
        }

        $_SESSION[self::SESSION_KEY] = (object)[
            'token' => $token,
            'token_secret' => $secret,
            'expires' => $expires
        ];
    }


    /**
     * Clears any existing session
     */
    function deleteSession()
    {
        if (isset($_SESSION[self::SESSION_KEY])) {
            unset($_SESSION[self::SESSION_KEY]);
        }
    }

}