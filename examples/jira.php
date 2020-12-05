<?php

/**
 * Example of making API calls for the Yahoo service
 *
 * @author     And <and.webdev@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials[ 'jira' ][ 'key' ],
    $servicesCredentials[ 'jira' ][ 'secret' ],
    $currentUri,
    $servicesCredentials[ 'jira' ][ 'privateKey' ]
);

// Instantiate the Yahoo service using the credentials, http client and storage mechanism for the token
$jiraService = $serviceFactory->createService('jira', $credentials, $storage);

if ($jiraService->isGlobalRequestArgumentsPassed()) {
    $result = $jiraService->retrieveAccessTokenByGlobReqArgs()->requestJSON('priority');

    echo 'result: <pre>' . print_r($result, true) . '</pre>';   //JIRA priority Result
} elseif (!empty($_GET[ 'go' ]) && $_GET[ 'go' ] === 'go') {
    $jiraService->redirectToAuthorizationUri();
} else {
    echo "<a href='$currentUri?go=go'>Login with Jira!</a>";
}
