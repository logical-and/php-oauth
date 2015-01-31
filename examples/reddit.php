<?php

/**
 * Example of retrieving an authentication token of the Reddit service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Connor Hindley <conn.hindley@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Reddit;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['reddit']['key'],
    $servicesCredentials['reddit']['secret'],
    $currentUri
);

// Instantiate the Reddit service using the credentials, http client and storage mechanism for the token
/** @var $reddit Reddit */
$reddit = $serviceFactory->createService('Reddit', $credentials, $storage, array(Reddit::SCOPE_IDENTITY));

if ($reddit->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $reddit->retrieveAccessTokenByGlobReqArgs()->requestJSON('/api/v1/me.json');

	// Show some of the resultant data
	echo 'Your unique reddit user id is: ' . $result['id'] . ' and your username is ' . $result['name'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$reddit->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Reddit!</a>";
}