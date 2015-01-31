<?php

/**
 * Bootstrap the library
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Setup error reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Setup the timezone
 */
ini_set('date.timezone', 'Europe/Amsterdam');

/**
 * Create a new instance of the URL class with the current URI, stripping the query string
 */
$currentUri = \League\Url\Url::createFromServer($_SERVER);
$currentUri->setQuery('');

/**
 * Load the credential for the different services
 */
require_once __DIR__ . '/init.php';
