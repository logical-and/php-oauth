<?php

/**
 * Example of making API calls for the ScoopIt service
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\ScoopIt;
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
	$servicesCredentials['scoopit']['key'],
	$servicesCredentials['scoopit']['secret'],
	$currentUri
);

// Instantiate the ScoopIt service using the credentials, http client and storage mechanism for the token
$scoopItService = $serviceFactory->createService('ScoopIt', $credentials, $storage);

if ($scoopItService->isGlobalRequestArgumentsPassed()) {
	$result = $scoopItService->retrieveAccessTokenByGlobReqArgs()->requestJSON('profile');

	echo 'result: <pre>' . print_r($result, true) . '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$scoopItService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with ScoopIt!</a>";
}
