<?php

/**
 * Example of retrieving an authentication token of the Twitter service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Twitter;
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
    $servicesCredentials['twitter']['key'],
    $servicesCredentials['twitter']['secret'],
    $currentUri
);

// Instantiate the twitter service using the credentials, http client and storage mechanism for the token
/** @var $twitterService Twitter */
$twitterService = $serviceFactory->createService('twitter', $credentials, $storage);

if ($twitterService->isGlobalRequestArgumentsPassed()) {
    // Send a request now that we have access token
    $result = $twitterService->retrieveAccessTokenByGlobReqArgs()->requestJSON('account/verify_credentials.json');

	echo 'Your extracted username: ' . $twitterService->constructExtractor()->getUsername();

	echo '<br>';
    echo 'Data dump: <pre>' . print_r($result, true) . '</pre>';


} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$twitterService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Twitter!</a>";
}