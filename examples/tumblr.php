<?php

/**
 * Example of retrieving an authentication token of the Tumblr service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Tumblr;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// We need to use a persistent storage to save the token, because oauth1 requires the token secret received before'
// the redirect (request token request) in the access token request.
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['tumblr']['key'],
    $servicesCredentials['tumblr']['secret'],
    $currentUri
);

// Instantiate the tumblr service using the credentials, http client and storage mechanism for the token
/** @var $tumblrService Tumblr */
$tumblrService = $serviceFactory->createService('tumblr', $credentials, $storage);

if ($tumblrService->isGlobalRequestArgumentsPassed()) {
	$result = $tumblrService->retrieveAccessTokenByGlobReqArgs()->requestJSON('user/info');

	echo 'result: <pre>' . print_r($result, true) . '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$tumblrService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Tumblr!</a>";
}