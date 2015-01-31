<?php

/**
 * Example of retrieving an authentication token of the Ustream service
 *
 * PHP version 5.4
 *
 * @author     Attila Gonda <pcdevil7@gmail.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Ustream;
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
    $servicesCredentials['ustream']['key'],
    $servicesCredentials['ustream']['secret'],
    $currentUri
);

// Instantiate the Ustream service using the credentials, http client and storage mechanism for the token
/** @var $ustream Ustream */
$ustream = $serviceFactory->createService('Ustream', $credentials, $storage, ['identity']);

if ($ustream->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
	$result = $ustream->retrieveAccessTokenByGlobReqArgs()->requestJSON('users/self.json');

	// Show some of the resultant data
	echo 'Your unique Ustream user id is: ' . $result['id'] . ' and your username is ' . $result['username'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	$ustream->redirectToAuthorizationUri();
} else {
	echo "<a href='$currentUri?go=go'>Login with Ustream!</a>";
}