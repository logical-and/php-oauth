<?php

/**
 * Example of retrieving an authentication token of the Dailymotion service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Mouhamed SEYE <mouhamed@seye.pro>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Dailymotion;
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
    $servicesCredentials['dailymotion']['key'],
    $servicesCredentials['dailymotion']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Dailymotion service using the credentials, http client, storage mechanism for the token and email scope
/** @var $dailymotionService Dailymotion */
$dailymotionService = $serviceFactory->createService('dailymotion', $credentials, $storage, array(Dailymotion::SCOPE_EMAIL));

if ($dailymotionService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $dailymotionService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/me?fields=email,id');

	// Show some of the resultant data
	echo 'Your unique Dailymotion user id is: ' . $result['id'] . ' and your email is ' . $result['email'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$dailymotionService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Dailymotion!</a>";
}