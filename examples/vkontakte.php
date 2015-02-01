<?php

/**
 * Example of retrieving an authentication token of the Bitly service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use Gregwar\Image\Image;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\OAuth2\Service\Vkontakte;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['vkontakte']['key'],
    $servicesCredentials['vkontakte']['secret'],
    $currentUri
);

// Instantiate the VKontakte service using the credentials, http client and storage mechanism for the token
/** @var $vkService Vkontakte */
$vkService = $serviceFactory->createService('vkontakte', $credentials, $storage, [Vkontakte::SCOPE_EMAIL]);

if ($vkService->isGlobalRequestArgumentsPassed()) {
	// Retrieve a token and send a request
    $result = $vkService->retrieveAccessTokenByGlobReqArgs()->requestJSON('users.get.json');

    // Show some of the resultant data
    echo 'Your unique user id is: ' . $result['response'][0]['uid'] . ' and your first name is ' . $result['response'][0]['first_name'];

	echo '<br />';
	$extractor = $vkService->constructExtractor();
	echo 'Your extracted url is a: ' .
		'<a target="_blank" href="' . $extractor->getProfileUrl() . '">' . $extractor->getProfileUrl() . '</a>, ' .
		'city is a: ' . $extractor->getLocation()['name'] . ',' .
		'<br> and image: <br>' . inline_image($extractor->getImageRawData(200));
} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $vkService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with VKontakte!</a>";
}