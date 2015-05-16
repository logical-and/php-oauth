<?php

/**
 * Example of retrieving an authentication token from the RunKeeper service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\RunKeeper;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'runkeeper' ][ 'key' ],
    $servicesCredentials[ 'runkeeper' ][ 'secret' ],
    $currentUri
);

// Instantiate the Runkeeper service using the credentials, http client and storage mechanism for the token
/** @var $runkeeperService RunKeeper */
$runkeeperService = $serviceFactory->createService('RunKeeper', $credentials, $storage, []);

if ($runkeeperService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $runkeeperService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/user');

    // Show some of the resultant data
    echo 'Your unique RunKeeper user id is: ' . $result[ 'userID' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $runkeeperService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with RunKeeper!</a>";
}
