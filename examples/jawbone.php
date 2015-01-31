<?php

/**
 * Example of retrieving an authentication token from the JawboneUP service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Andrii Gakhov <andrii.gakhov@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\JawboneUP;
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
    $servicesCredentials['jawbone']['key'],
    $servicesCredentials['jawbone']['secret'],
    $currentUri
);

// Instantiate the Jawbone UP service using the credentials, http client and storage mechanism for the token
/** @var $jawboneService JawboneUP */
$jawboneService = $serviceFactory->createService('JawboneUP', $credentials, $storage, array());

if ($jawboneService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $jawboneService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/users/@me');

	// Show some of the resultant data
	echo 'Your unique Jawbone UP user id is: ' . $result['data']['xid'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$jawboneService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Jawbone UP!</a>";
}
