<?php

/**
 * Example of retrieving an authentication token of the FitBit service
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
use OAuth\OAuth1\Service\FitBit;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'fitbit' ][ 'key' ],
    $servicesCredentials[ 'fitbit' ][ 'secret' ],
    $currentUri
);

// Instantiate the FitBit service using the credentials, http client and storage mechanism for the token
/** @var $fitbitService FitBit */
$fitbitService = $serviceFactory->createService('FitBit', $credentials, $storage);

if ($fitbitService->isGlobalRequestArgumentsPassed()) {
    $result = $fitbitService->retrieveAccessTokenByGlobReqArgs()->requestJSON('user/-/profile.json');

    echo 'result: <pre>' . print_r($result, true) . '</pre>';
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $fitbitService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with FitBit!</a>";
}
