<?php

/**
 * Example of retrieving an authentication token of the Bitly service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Bitly;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'bitly' ][ 'key' ],
    $servicesCredentials[ 'bitly' ][ 'secret' ],
    $currentUri
);

// Instantiate the Bitly service using the credentials, http client and storage mechanism for the token
/** @var $bitlyService Bitly */
$bitlyService = $serviceFactory->createService('bitly', $credentials, $storage);

if ($bitlyService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $bitlyService->retrieveAccessTokenByGlobReqArgs()->requestJSON('user/info');

    // Show some of the resultant data
    echo 'Your unique user id is: ' .
        $result[ 'data' ][ 'login' ] .
        ' and your name is ' .
        $result[ 'data' ][ 'display_name' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $bitlyService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Bitly!</a>";
}
