<?php

/**
 * Example of retrieving an authentication token of the Facebook service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Benjamin Bender <bb@codepoet.de>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\UserData\ExtractorFactory;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['facebook']['key'],
    $servicesCredentials['facebook']['secret'],
    $currentUri
);

// Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
/** @var $facebookService Facebook */
$facebookService = $serviceFactory->createService('facebook', $credentials, $storage, array(Facebook::SCOPE_EMAIL));

if ($facebookService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $facebookService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/me');

	// Show some of the resultant data
	echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

	echo '<br />';
	echo 'Your extracted email is a: ' . $facebookService->constructExtractor()->getEmail();

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$facebookService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Facebook!</a>";
}