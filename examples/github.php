<?php

/**
 * Example of retrieving an authentication token of the Github service
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
use OAuth\OAuth2\Service\GitHub;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'github' ][ 'key' ],
    $servicesCredentials[ 'github' ][ 'secret' ],
    $currentUri
);

// Instantiate the GitHub service using the credentials, http client and storage mechanism for the token
/** @var $gitHub GitHub */
$gitHub = $serviceFactory->createService('GitHub', $credentials, $storage, [GitHub::SCOPE_USER]);

if ($gitHub->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $gitHub->retrieveAccessTokenByGlobReqArgs()->requestJSON('user/emails');

    // Show some of the resultant data
    echo 'The first email on your github account is ' . $result[ 0 ][ 'email' ];

    echo '<br />';
    echo 'Your extracted username is a: ' . $gitHub->constructExtractor()->getUsername();
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $gitHub->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Github!</a>";
}
