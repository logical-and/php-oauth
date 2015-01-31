<?php

/**
 * Example of retrieving an authentication token of the Flickr service
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Christian Mayer <thefox21at@gmail.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Flickr;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	$servicesCredentials['flickr']['key'],
	$servicesCredentials['flickr']['secret'],
	$currentUri
);

// Instantiate the Flickr service using the credentials, http client and storage mechanism for the token
$flickrService = $serviceFactory->createService('Flickr', $credentials, $storage);

if ($flickrService->isGlobalRequestArgumentsPassed()) {
	$flickrService->retrieveAccessTokenByGlobReqArgs();

	$xml = simplexml_load_string($flickrService->request('flickr.test.login'));
	print "status: ".(string)$xml->attributes()->stat."\n";

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
	header('Location: '. $flickrService->getAuthorizationUri(['perms' => 'write']));
} else {
	echo "<a href='$currentUri?go=go'>Login with Flickr!</a>";
}