<?php

/**
 * Example of making API calls for the Yahoo service
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'yahoo' ][ 'key' ],
    $servicesCredentials[ 'yahoo' ][ 'secret' ],
    $currentUri
);

// Instantiate the Yahoo service using the credentials, http client and storage mechanism for the token
$yahooService = $serviceFactory->createService('Yahoo', $credentials, $storage);

if ($yahooService->isGlobalRequestArgumentsPassed()) {
    $result = $yahooService->retrieveAccessTokenByGlobReqArgs()->requestJSON('profile');

    echo 'result: <pre>' . print_r($result, true) . '</pre>';
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $yahooService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Yahoo!</a>";
}
