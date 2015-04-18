<?php

/**
 * Example of retrieving an authentication token of the DevianArt service
 *
 * PHP version 5.4
 *
 * @author     @author Charlotte Genevier (https://github.com/cgenevier)
 * @author     And <and.webdev@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\DeviantArt;
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
    $servicesCredentials['devianart']['key'],
    $servicesCredentials['devianart']['secret'],
    $currentUri
);

// Instantiate the DevianArt service using the credentials, http client and storage mechanism for the token
/** @var $devianArtService DeviantArt */
$devianArtService = $serviceFactory->createService('DevianArt', $credentials, $storage, []);

if ($devianArtService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $devianArtService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/user/whoami');

	// Show some of the resultant data
	echo 'Your DeviantArt username is: ' . $result['username'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$devianArtService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with DeviantArt!</a>";
}