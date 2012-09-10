<?php

namespace GW2Spidy;

use GW2Spidy\Util\Singleton;

use GW2Spidy\Util\CurlRequest;
use \Exception;

class GW2LoginManager extends Singleton {
    protected $loggedIn;
    protected $sid;

    public function __construct() {}
    public function __destruct() {
        $this->doLogout();
    }

    public function ensureLogin() {
        if (!$this->loggedIn) {
            $this->loggedIn = $this->doLogin();
        }
    }

    public function doLogin() {
        $curl = CurlRequest::newInstance(AUTH_URL . "/login")
                    ->setOption(CURLOPT_POST, true)
                    ->setOption(CURLOPT_POSTFIELDS, http_build_query(array('email' => LOGIN_EMAIL, 'password' => LOGIN_PASSWORD)))
                    ->exec()
                    ;

        if ($sid = $curl->getResponseCookies('s')) {
            $this->sid = $sid;
        } else {
            throw new Exception("Login request failed, no SID.");
        }

        return true;
    }

    public function doLogout() {
        try {
            $curl = CurlRequest::newInstance(AUTH_URL . "/logout")
                        ->exec()
                        ;
        } catch (Exception $e) {
            // no1 cares
        }
    }

    public function getSessionID() {
        $this->ensureLogin();

        return $this->sid;
    }
}

?>
