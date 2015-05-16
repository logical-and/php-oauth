<?php

/**
 * Example of retrieving an authentication token of the Instagram service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Hannes Van De Vreken <vandevreken.hannes@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Instagram;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'instagram' ][ 'key' ],
    $servicesCredentials[ 'instagram' ][ 'secret' ],
    $currentUri
);

$scopes = ['basic', 'comments', 'relationships', 'likes'];

// Instantiate the Instagram service using the credentials, http client and storage mechanism for the token
/** @var $instagramService Instagram */
$instagramService = $serviceFactory->createService('instagram', $credentials, $storage, $scopes);

if ($instagramService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $instagramService->retrieveAccessTokenByGlobReqArgs()->requestJSON('users/self');

    // Show some of the resultant data
    echo 'Your unique instagram user id is: ' .
        $result[ 'data' ][ 'id' ] .
        ' and your name is ' .
        $result[ 'data' ][ 'full_name' ];

    echo '<br />';
    echo 'Your extracted email is a: ' . $instagramService->constructExtractor()->getEmail();
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $instagramService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Instagram!</a>";
}
