<?php

/**
 * Example of retrieving an authentication token of the Linkedin service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Antoine Corcy <contact@sbin.dk>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Linkedin;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'linkedin' ][ 'key' ],
    $servicesCredentials[ 'linkedin' ][ 'secret' ],
    $currentUri
);

// Instantiate the Linkedin service using the credentials, http client and storage mechanism for the token
/** @var $linkedinService Linkedin */
$linkedinService = $serviceFactory->createService('linkedin', $credentials, $storage, ['r_basicprofile']);

if ($linkedinService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $linkedinService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/people/~?format=json');

    // Show some of the resultant data
    echo 'Your linkedin first name is ' . $result[ 'firstName' ] . ' and your last name is ' . $result[ 'lastName' ];

    echo '<br />';
    echo 'Your extracted e-mail is a: ' . $linkedinService->constructExtractor()->getEmail();
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $linkedinService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Linkedin!</a>";
}
