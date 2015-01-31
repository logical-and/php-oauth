<?php

/**
 * Example of retrieving an authentication token of the Spotify service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Craig Morris <craig.michael.morris@gmail.com>
 * @author     Ben King <ben.kingsy@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Spotify;
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
    $servicesCredentials['spotify']['key'],
    $servicesCredentials['spotify']['secret'],
    $currentUri
);

// Instantiate the Spotify service using the credentials, http client and storage mechanism for the token
/** @var $spotifyService Spotify */
$spotifyService = $serviceFactory->createService('spotify', $credentials, $storage);

if ($spotifyService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $spotifyService->retrieveAccessTokenByGlobReqArgs()->requestJSON('me');

	// Show some of the resultant data
	echo 'Your unique user id is: ' . $result['id'] . ' and your name is ' . $result['display_name'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$spotifyService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Spotify!</a>";
}
