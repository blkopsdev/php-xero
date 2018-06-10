<?php

return [
    'oauth' => [
        //This is just for demo purposes, it should be set from a more reliable source for production use
        'callback'        => "http://$_SERVER[HTTP_HOST]/application/callback",

        'consumer_key'    => 'YOUR_CONSUMER_KEY',
        'consumer_secret' => 'YOUR_CONSUMER_SECRET',

        //This is required for public applications, as Xero doesn't accept the signature as a header for some reason
        'signature_location'  => \XeroPHP\Remote\OAuth\Client::SIGN_LOCATION_QUERY,
    ],
    'curl' => [
        CURLOPT_USERAGENT   => 'XeroPHP Sample App',
        CURLOPT_CAINFO => APP_ROOT . '/certs/ca-bundle.crt',
    ],
];


