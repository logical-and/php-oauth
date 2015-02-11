<?php

/**
 * Example of retrieving an authentication token of the Eve Online service
 * PHP version 5.4
 * @author     Micahel Cummings <mgcummings@yahoo.com>
 * @author     And <and.webdev@gmail.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\EveOnline;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	$servicesCredentials['eveonline']['key'],
	$servicesCredentials['eveonline']['secret'],
	$currentUri
);

// Instantiate the Eve Online service using the credentials, http client, storage mechanism for the token and profile scope
/** @var EveOnline $eveService */
$eveService = $serviceFactory->createService('eveonline', $credentials, $storage, array(''));

if ($eveService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $eveService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/oauth/verify');

	// Show some of the resultant data
	print 'CharacterName: ' . $result['CharacterName'] . PHP_EOL
		. 'CharacterID: ' . $result['CharacterID'] . PHP_EOL
		. 'ExpiresOn: ' . $result['ExpiresOn'] . PHP_EOL
		. 'Scopes: ' . $result['Scopes'] . PHP_EOL
		. 'TokenType: ' . $result['TokenType'] . PHP_EOL
		. 'CharacterOwnerHash: ' . $result['CharacterOwnerHash'] . PHP_EOL;

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$eveService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Eve Online!</a>";
}