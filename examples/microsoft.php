<?php

/**
 * Example of retrieving an authentication token of the Microsoft service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Microsoft;
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
    $servicesCredentials['microsoft']['key'],
    $servicesCredentials['microsoft']['secret'],
    $currentUri
);

// Instantiate the Microsoft service using the credentials, http client and storage mechanism for the token
/** @var $microsoft Microsoft */
$microsoft = $serviceFactory->createService('microsoft', $credentials, $storage, array(Microsoft::SCOPE_BASIC));

if ($microsoft->isGlobalRequestArgumentsPassed()) {
	// This was a callback request from Microsoft, get the token
	var_dump($microsoft->retrieveAccessTokenByGlobReqArgs()->getAccessToken());

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$microsoft->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Microsoft!</a>";
}