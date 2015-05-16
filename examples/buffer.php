<?php

/**
 * Example of retrieving an authentication token of the Buffer service
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
use OAuth\OAuth2\Service\Buffer;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'buffer' ][ 'key' ],
    $servicesCredentials[ 'buffer' ][ 'secret' ],
    $currentUri
);

// Instantiate the buffer service using the credentials, http client and storage mechanism for the token
/** @var $bufferService buffer */
$bufferService = $serviceFactory->createService('buffer', $credentials, $storage);

if ($bufferService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $bufferService->retrieveAccessTokenByGlobReqArgs()->requestJSON('user.json');

    // Show some of the resultant data
    echo 'Your unique user id is: ' . $result[ 'id' ] . ' and your plan is ' . $result[ 'plan' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $bufferService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with buffer!</a>";
}
