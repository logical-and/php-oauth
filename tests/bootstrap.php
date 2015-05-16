<?php
/**
 * Bootstrap the tests
 *
 * PHP version 5.4
 *
 * @category   OAuthTest
 * @author     And <and.webdev@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     David Desberg  <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuthTest;

use Composer\Autoload\ClassLoader;

// Setting up the default timezone. because well... PHP sucks
date_default_timezone_set('Europe/Amsterdam');

/**
 * Fire up the autoloader
 */
/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

// Setting up composer autoloader for the OAuth mocks
$loader->addPsr4(__NAMESPACE__ . '\\', __DIR__ . '/');
