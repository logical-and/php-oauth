<?php

/**
 * Example of retrieving an authentication token of the PayPal service
 *
 * PHP version 5.4
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Flávio Heleno <flaviohbatista@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Paypal;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'paypal' ][ 'key' ],
    $servicesCredentials[ 'paypal' ][ 'secret' ],
    $currentUri
);

// Instantiate the PayPal service using the credentials, http client, storage mechanism for the token and profile/openid scopes
/** @var $paypalService PayPal */
$paypalService =
    $serviceFactory->createService('paypal', $credentials, $storage, [PayPal::SCOPE_PROFILE, Paypal::SCOPE_OPENID]);

if ($paypalService->isGlobalRequestArgumentsPassed()) {
    // Retrieve a token and send a request
    $result = $paypalService->retrieveAccessTokenByGlobReqArgs()->requestJSON(
        '/identity/openidconnect/userinfo/?schema=openid'
    );

    // Show some of the resultant data
    echo 'Your unique PayPal user id is: ' . $result[ 'user_id' ] . ' and your name is ' . $result[ 'name' ];
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $paypalService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with PayPal!</a>";
}
