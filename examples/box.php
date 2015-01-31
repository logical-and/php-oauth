<?php

/**
 * Example of retrieving an authentication token of the Box service
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

use OAuth\OAuth2\Service\Box;
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
    $servicesCredentials['box']['key'],
    $servicesCredentials['box']['secret'],
    $currentUri
);

// Instantiate the Box service using the credentials, http client and storage mechanism for the token
/** @var $boxService Box */
$boxService = $serviceFactory->createService('box', $credentials, $storage);

if ($boxService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $boxService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/users/me');

	// Show some of the resultant data
	echo 'Your Box name is ' . $result['name'] . ' and your email is ' . $result['login'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$boxService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Box!</a>";
}
