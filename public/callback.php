<?php
	require __DIR__ . '/vendor/autoload.php';

	use XeroPHP\Application\PublicApplication;
	use XeroPHP\Remote\Request;
	use XeroPHP\Remote\URL;
	require_once('storage.php');

	// Storage Classe uses sessions for storing token > extend to your DB of choice
	$storage = new StorageClass();	

	$config = [
	    'oauth' => [
	        'callback'        => 'http://localhost/myapp/callback.php',
	        'consumer_key'    => 'YOUR_CONSUMER_KEY',
	        'consumer_secret' => 'YOUR_CONSUMER_SECRET',
	        'signature_location'  => \XeroPHP\Remote\OAuth\Client::SIGN_LOCATION_QUERY,
		    ],
	    'curl' => [
	        CURLOPT_USERAGENT   => 'xero-php sample app',
	        CURLOPT_CAINFO => __DIR__ . '/certs/ca-bundle.crt',
	    ],
	];
	
	$xero = new PublicApplication($config);

	//if get session data
	$oauth_session = $storage->getSession();

	// Check if your session has a token
	if (isset( $oauth_session['token'])) {

	    $xero->getOAuthClient()
	        ->setToken($oauth_session['token'])
	        ->setTokenSecret($oauth_session['token_secret']);
	    
	    if (isset($_REQUEST['oauth_verifier'])) {

	    	#echo "Verifier Found - Swap Request for Access token<BR><BR>";   
	        $xero->getOAuthClient()->setVerifier($_REQUEST['oauth_verifier']);
	        $url = new URL($xero, URL::OAUTH_ACCESS_TOKEN);
	        $request = new Request($xero, $url);
	        $request->send();
	        $oauth_response = $request->getResponse()->getOAuthResponse();
	     
	        $storage->setOAuthTokenSession(
		        $oauth_response['oauth_token'],
	            $oauth_response['oauth_token_secret'],
	            $oauth_response['oauth_expires_in'],
	            $oauth_response['oauth_session_handle'],
	            (new \DateTime())->format('Y-m-d H:i:s')
		    );
  
		    header("Location: get.php");
	        exit;
  
		}
	} else {
	    header("Location: index.php?error=true");
	}
	?>
	<html>
	<head>
		<title>My App</title>
	</head>
	<body>		
		Opps! Should have redirected to <a href="get.php">to this page</a>
	</body>
</html>
