<?php

/**
 * Example of retrieving an authentication token of the Etsy service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Iñaki Abete <inakiabt+github@gmail.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Etsy;
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
    $servicesCredentials['etsy']['key'],
    $servicesCredentials['etsy']['secret'],
    $currentUri
);

// Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
/** @var $etsyService Etsy */
$etsyService = $serviceFactory->createService('Etsy', $credentials, $storage);

if ($etsyService->isGlobalRequestArgumentsPassed()) {
	$result = $etsyService->retrieveAccessTokenByGlobReqArgs()->requestJSON('users/__SELF__');

	$extractor = $etsyService->constructExtractor();
	echo 'Extracted name: ' . $extractor->getFullName() .
		', email: ' . $extractor->getEmail() .
		', location: ' . $extractor->getLocation() .
		', profile url: ' . $extractor->getProfileUrl();

	echo '<br>';
	echo 'result: <pre>' . print_r($result, true) . '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$etsyService->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Etsy!</a>";
}