<?php

/**
 * Example of retrieving an authentication token of the Dropbox service
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
use OAuth\OAuth2\Service\Dropbox;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'dropbox' ][ 'key' ],
    $servicesCredentials[ 'dropbox' ][ 'secret' ],
    $currentUri
);

// Instantiate the Dropbox service using the credentials, http client and storage mechanism for the token
/** @var $dropboxService Dropbox */
$dropboxService = $serviceFactory->createService('dropbox', $credentials, $storage, []);

if ($dropboxService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $dropboxService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/account/info');

    // Show some of the resultant data
    echo 'Your unique Dropbox user id is: ' . $result[ 'uid' ] . ' and your name is ' . $result[ 'display_name' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $dropboxService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Dropbox!</a>";
}
