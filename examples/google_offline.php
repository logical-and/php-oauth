<?php

/**
 * Example of retrieving an authentication token of the Google service
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
use OAuth\OAuth2\Service\Google;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'google' ][ 'key' ],
    $servicesCredentials[ 'google' ][ 'secret' ],
    $currentUri
);

// Instantiate the Google service using the credentials, http client and storage mechanism for the token
/** @var Google $googleService */
$googleService = $serviceFactory->createService(
    'google',
    $credentials,
    $storage,
    [Google::SCOPE_USERINFO_EMAIL, Google::SCOPE_USERINFO_PROFILE]
);

if (!empty($_GET[ 'clear' ]) && $_GET[ 'clear' ] === 'clear') {
    $googleService->getStorage()->clearToken($googleService->service());
    header("Location: $currentUri", true);
} elseif ($googleService->isGlobalRequestArgumentsPassed() or
    $googleService->getStorage()->hasAccessToken($googleService->service())
) {
    if (!$googleService->getStorage()->hasAccessToken($googleService->service())) {
        $googleService->retrieveAccessTokenByGlobReqArgs();
    }

    // Retrieve a token and send a request
    $result = $googleService->requestJSON('https://www.googleapis.com/oauth2/v1/userinfo');

    // Show some of the resultant data
    echo 'Your unique google user id is: ' . $result[ 'id' ] . ' and your name is ' . $result[ 'name' ];

    echo '<br />';
    $extractor = $googleService->constructExtractor();
    echo 'Your extracted email is a: ' . $extractor->getEmail() . ', ' .
        '<br>and image: ' . inline_image($extractor->getImageRawData(50));

    echo '<br />';
    echo '<br />';
    echo "<a href='$currentUri?clear=clear'>Clear offline access token</a>";
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $googleService->setAccessType('offline')->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Google!</a>";
}
