<?php

/**
 * This file sets up the information needed to test the examples in different environments.
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * @var array A list of all the credentials to be used by the different services in the examples
 */
$servicesCredentials = [
    'amazon'      => [
        'key'    => '',
        'secret' => '',
    ],
    'bitbucket'   => [
        'key'    => '',
        'secret' => '',
    ],
    'bitly'       => [
        'key'    => '',
        'secret' => '',
    ],
    'box'         => [
        'key'    => '',
        'secret' => '',
    ],
    'buffer'      => [
        'key'    => '',
        'secret' => '',
    ],
    'dailymotion' => [
        'key'    => '',
        'secret' => '',
    ],
    'dropbox'     => [
        'key'    => '',
        'secret' => '',
    ],
    'etsy'        => [
        'key'    => '',
        'secret' => '',
    ],
    'eveonline'   => [
        'key'    => '',
        'secret' => '',
    ],
    'facebook'    => [
        'key'    => '',
        'secret' => '',
    ],
    'fitbit'      => [
        'key'    => '',
        'secret' => '',
    ],
    'flickr'      => [
        'key'    => '',
        'secret' => '',
    ],
    'foursquare'  => [
        'key'    => '',
        'secret' => '',
    ],
    'github'      => [
        'key'    => '',
        'secret' => '',
    ],
    'google'      => [
        'key'    => '',
        'secret' => '',
    ],
    'instagram'   => [
        'key'    => '',
        'secret' => '',
    ],
    'linkedin'    => [
        'key'    => '',
        'secret' => '',
    ],
    'mailchimp'   => [
        'key'    => '',
        'secret' => '',
    ],
    'microsoft'   => [
        'key'    => '',
        'secret' => '',
    ],
    'paypal'      => [
        'key'    => '',
        'secret' => '',
    ],
    'pocket'      => [
        'key' => '',
    ],
    'reddit'      => [
        'key'    => '',
        'secret' => '',
    ],
    'runkeeper'   => [
        'key'    => '',
        'secret' => '',
    ],
    'scoopit'     => [
        'key'    => '',
        'secret' => ''
    ],
    'soundcloud'  => [
        'key'    => '',
        'secret' => '',
    ],
    'spotify'     => [
        'key'    => '',
        'secret' => '',
    ],
    'tumblr'      => [
        'key'    => '',
        'secret' => '',
    ],
    'twitter'     => [
        'key'    => '',
        'secret' => '',
    ],
    'ustream'     => [
        'key'    => '',
        'secret' => '',
    ],
    'yahoo'       => [
        'key'    => '',
        'secret' => ''
    ],
    'yammer'      => [
        'key'    => '',
        'secret' => ''
    ],
    'vkontakte'   => [
        'key'    => '',
        'secret' => ''
    ],
    'jira'         => [
        'key'    => '',
        'secret' => '',
        'privateKey' => '', //Path to rsa.pem file for example: 'keys/rsa.pem'
    ],
];

/** @var $serviceFactory \OAuth\ServiceFactory An OAuth service factory. */
$serviceFactory = new \OAuth\ServiceFactory();
