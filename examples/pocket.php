<?php

/**
 * Example of retrieving an authentication token of the Pocket service.
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Christian Mayer <thefox21at@gmail.com>
 * @copyright  Copyright (c) 2014 Christian Mayer <thefox21at@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Url;
use OAuth\Common\Storage\Session;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

/** @var Url $currentUri */

// Setup the credentials for the requests
$credentials = new Credentials(
	$servicesCredentials[ 'pocket' ][ 'key' ],
	NULL, // Pocket API doesn't have a secret key. :S
	$currentUri->getBaseUrl()  . '/' . $currentUri->getPath() . '?step=3'
);

// Instantiate the Pocket service using the credentials, http client and storage mechanism for the token
$pocketService = $serviceFactory->createService('Pocket', $credentials, $storage);

if ($pocketService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token
	/** @var \OAuth\Common\Token\TokenInterface $token */
	$token = $pocketService->retrieveAccessTokenByGlobReqArgs()->getAccessToken();

	// Show some of the resultant data
	print 'User: '.$token->getExtraParams()['username'].'<br />';
	print 'Access Token: '.$token->getAccessToken();

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$pocketService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Pocket!</a>";
}