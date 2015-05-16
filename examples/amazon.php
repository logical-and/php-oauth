<?php

/**
 * Example of retrieving an authentication token of the Amazon service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Flávio Heleno <flaviohbatista@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Amazon;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'amazon' ][ 'key' ],
    $servicesCredentials[ 'amazon' ][ 'secret' ],
    $currentUri
);

// Instantiate the Amazon service using the credentials, http client, storage mechanism for the token and profile scope
$amazonService = $serviceFactory->createService('amazon', $credentials, $storage, [Amazon::SCOPE_PROFILE]);

if ($amazonService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $amazonService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/user/profile');

    // Show some of the resultant data
    echo 'Your unique Amazon user id is: ' . $result[ 'user_id' ] . ' and your name is ' . $result[ 'name' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $amazonService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Amazon!</a>";
}
