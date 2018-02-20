<?php
class StorageClass
{
	function __construct() {
		if( !isset($_SESSION) ){
        	$this->init_session();
    	}
   	}

   	public function init_session(){
    	session_start();
	}

    public function getSession() {
    	return $_SESSION['oauth'];
    }

 	public function startSession($token, $secret, $expires = null)
	{
       	session_start();
	}

    public function setOAuthSession($token, $secret, $expires = null)
	{

		// expires sends back an int
	    if ($expires !== null) {
	        $expires = time() + intval($expires);
	    }
		
	    $_SESSION['oauth'] = [
	        'token' => $token,
	        'token_secret' => $secret,
	        'expires' => $expires
	    ];
	}


	public function setOAuthTokenSession($token, $secret, $expires = null,$session_handle,$token_timestamp)
	{
	    if ($expires !== null) {
	        $expires = time() + intval($expires);
	    }
	    $_SESSION['oauth'] = [
	        'token' => $token,
	        'token_secret' => $secret,
	        'expires' => $expires,
	        'session_handle' => $session_handle,
	        'token_timestamp' => $token_timestamp
	    ];
	}

	public function getOAuthSession()
	{
	    //If it doesn't exist or is expired, return null
	    if (!empty($this->getSession())
	        || ($_SESSION['oauth']['token_timestamp'] !== null
	        && $_SESSION['oauth']['token_timestamp'] <= time())
	    ) {
	        return null;
	    }
	    return $this->getSession();
	}

	public function checkToken($xero) 
	{

		if (!empty($this->getSession()) || ($_SESSION['oauth']['token_timestamp'] !== null)) 
		{
			$expire = date("Y-m-d H:i:s", strtotime("-30 minutes"));
			$tokenTimestamp = $_SESSION['oauth']['token_timestamp'];

			if ($expire > $tokenTimestamp) {
		  		return true;
			} else {
				return false;
			}

				
		} else {
			return true;
		}
			
	}

	public function refreshToken($xero)
	{					
		$xero->getOAuthClient();
		$url = new URL($xero, URL::OAUTH_ACCESS_TOKEN);
        $request = new Request($xero, $url);
        $request->setParameter("oauth_session_handle",$oauth_session['session_handle']);
        $request->setParameter("oauth_token",$oauth_session['token']);

        $request->send();
        $oauth_response = $request->getResponse()->getOAuthResponse();
			
        $this->setOAuthTokenSession(
            $oauth_response['oauth_token'],
            $oauth_response['oauth_token_secret'],
            $oauth_response['oauth_expires_in'],
            $oauth_response['oauth_session_handle'],
            (new \DateTime())->format('Y-m-d H:i:s')
         );

	}
}
?>
