<?php

/**
 * Example of retrieving an authentication token of the Google service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Google;
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
    $servicesCredentials['google']['key'],
    $servicesCredentials['google']['secret'],
    $currentUri
);

// Instantiate the Google service using the credentials, http client and storage mechanism for the token
$googleService = $serviceFactory->createService('google', $credentials, $storage,
	array(Google::SCOPE_USERINFO_EMAIL, Google::SCOPE_USERINFO_PROFILE));

if ($googleService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $googleService->retrieveAccessTokenByGlobReqArgs()->requestJSON('https://www.googleapis.com/oauth2/v1/userinfo');

    // Show some of the resultant data
    echo 'Your unique google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

	echo '<br />';
	$extractor = $googleService->constructExtractor();
	echo 'Your extracted email is a: ' . $extractor->getEmail() . ', ' .
		'<br>and image: ' . inline_image($extractor->getImageRawData(50));

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $googleService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Google!</a>";
}
