<?php

/**
 * Example of retrieving an authentication token from the BitBucket service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Ændrew Rininsland <me@aendrew.com>
 *
 * Shamelessly cribbed from work by:
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth1\Service\BitBucket;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// We need to use a persistent storage to save the token, because oauth1 requires the token secret received before'
// the redirect (request token request) in the access token request.
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'bitbucket' ][ 'key' ],
    $servicesCredentials[ 'bitbucket' ][ 'secret' ],
    $currentUri
);

// Instantiate the BitBucket service using the credentials, http client and storage mechanism for the token
/** @var $bbService BitBucket */
$bbService = $serviceFactory->createService('BitBucket', $credentials, $storage);

if ($bbService->isGlobalRequestArgumentsPassed()) {
    $result = $bbService->retrieveAccessTokenByGlobReqArgs()->requestJSON('user/repositories');

    echo('The first repo in the list is ' . $result[ 0 ][ 'name' ]);
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $bbService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with BitBucket!</a>";
}
