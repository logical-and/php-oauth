<?php

/**
 * Example of retrieving an authentication token of the BattleNet service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\BattleNet;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'battlenet' ][ 'key' ],
    $servicesCredentials[ 'battlenet' ][ 'secret' ],
    $currentUri
);

// Instantiate the BattleNet service using the credentials, http client, storage mechanism for the token and profile scope
/** @var BattleNet $battleNetService */
$battleNetService = $serviceFactory->createService(
    'battlenet',
    $credentials,
    $storage,
    [BattleNet::SCOPE_SC2_PROFILE, BattleNet::SCOPE_WOW_PROFILE]
);
$battleNetService->setRegion('us');

if ($battleNetService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $battleNetService->retrieveAccessTokenByGlobReqArgs()->requestJSON('/account/user');

    // Show some of the resultant data
    echo 'Your unique Battle.Net user id is: ' . $result[ 'id' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $battleNetService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Battle.Net!</a>";
}
