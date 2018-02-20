<?php
	require __DIR__ . '/vendor/autoload.php';

	use XeroPHP\Application\PublicApplication;	
	use XeroPHP\Remote\Request;
	use XeroPHP\Remote\URL;
	require_once('storage.php');

	// Storage Classe uses sessions for storing token > extend to your DB of choice
	$storage = new StorageClass();

	/* PARTNER APP
	$config = [
	    'oauth' => [
	        'callback'        => 'http://localhost/myapp/callback.php',
	        'consumer_key'    => 'YOUR_CONSUMER_KEY',
	        'consumer_secret' => 'YOUR_CONSUMER_SECRET',
	        'rsa_private_key'       => 'file://certs/privatekey.pem',
	        'signature_location'  => \XeroPHP\Remote\OAuth\Client::SIGN_LOCATION_QUERY,
		    ],
	    'curl' => [
	        CURLOPT_USERAGENT   => 'xero-php sample app',
	        CURLOPT_CAINFO => __DIR__.'/certs/ca-bundle.crt',
	    ],
	];
	$xero = new PartnerApplication($config);
	*/
	// PUBLIC
	$config = [
	    'oauth' => [
	        'callback'        => 'http://localhost/myapp/callback.php',
	        'consumer_key'    => 'YOUR_CONSUMER_KEY',
	        'consumer_secret' => 'YOUR_CONSUMER_SECRET',
	        'signature_location'  => \XeroPHP\Remote\OAuth\Client::SIGN_LOCATION_QUERY,
		    ],
	    'curl' => [
	        CURLOPT_USERAGENT   => 'xero-php sample app',
	        CURLOPT_CAINFO => __DIR__.'/certs/ca-bundle.crt',
	    ],
	];
	
	$xero = new PublicApplication($config);
	
	if (empty($storage->getOAuthSession())) {

	    $url = new URL($xero, URL::OAUTH_REQUEST_TOKEN);
	    $request = new Request($xero, $url);

	    //Here's where you'll see if your keys are valid.
	    //You can catch a BadRequestException.
	    try {
	        $request->send();
	    } catch (Exception $e) {
	        print_r($e);
	        if ($request->getResponse()) {
	            print_r($request->getResponse()->getOAuthResponse());
	        }
	    }
	    $oauth_response = $request->getResponse()->getOAuthResponse();
	    $storage->setOAuthSession(
	        $oauth_response['oauth_token'],
	        $oauth_response['oauth_token_secret']
	    );

		$authorize_url = $xero->getAuthorizeURL($oauth_response['oauth_token']);
		header("Location: " .$authorize_url);
	}

	?>

	<html>
	<head>
		<title>My App</title>
	</head>
	<body>
		Opps! Problem redirecting .....
	</body>
</html>
