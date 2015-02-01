php-oauth
===========
This library provides oAuth support in PHP 5.4+ and is very easy to integrate with any project which requires an oAuth client.
It doesn't require any framework, so you are free on that

[![Latest Stable Version](https://poser.pugx.org/and/oauth/v/stable.png)](https://packagist.org/packages/and/oauth)
[![Total Downloads](https://poser.pugx.org/and/oauth/downloads.png)](https://packagist.org/packages/and/oauth)

Installation
------------
This library can be found on [Packagist](https://github.com/logical-and/php-oauth).
The recommended way to install this is through [composer](http://getcomposer.org).

Edit your `composer.json` and add:

```json
{
    "require": {
        "and/oauth": "*"
    }
}
```

And install dependencies:

```bash
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

Features
--------
- PSR-4 compliant for easy interoperability
- Fully extensible in every facet.
    - You can implement any service with any custom requirements by extending the protocol version's `AbstractService` implementation.
    - You can use any HTTP client you desire, just create a class utilizing it which implements `Buzz\Client\ClientInterface` (two implementations are included)
    - You can use any storage mechanism for tokens. By default, session, in-memory and Redis.io (requires PHPRedis) storage mechanisms are included. Implement additional mechanisms by implementing `OAuth\Common\Token\TokenStorageInterface`.
- Library are based on [lusitanian/oauth](https://github.com/Lusitanian/PHPoAuthLib) and [oryzone/oauth-user-data](https://github.com/Oryzone/PHPoAuthUserData) libraries. Here is key differences:
    - php-oauth uses third-party library for anything that aren't oauth logic, like URL object, HTTP clients, etc
    - php-oauth goal is simplicity, just look in examples
    - php-oauth makes remote providers is the same by giving your the same api and steps

Service support
---------------
The library supports both oAuth 1.x and oAuth 2.0 compliant services. A list of currently implemented services can be found below. 

Included service implementations
--------------------------------
- OAuth1
    - BitBucket
    - Etsy
    - FitBit
    - Flickr
    - Scoop.it!
    - Tumblr
    - Twitter
    - Xing
    - Yahoo
- OAuth2
    - Amazon
    - BitLy
    - Box
    - Dailymotion
    - Dropbox
    - Facebook
    - Foursquare
    - GitHub
    - Google
    - Harvest
    - Heroku
    - Instagram
    - Jawbone UP
    - LinkedIn
    - Mailchimp
    - Microsoft
    - PayPal
    - Pocket
    - Reddit
    - RunKeeper
    - SoundCloud
    - Spotify
    - Ustream
    - Vkontakte
    - Yammer
- more to come!

Examples
--------
Examples of basic usage are located in the examples/ directory.

Usage
------
For usage with complete auth flow, please see the examples.

UserData
--------
OAuth 1 and 2 are great standard protocols to authenticate users in our apps and the php-oauth
allow us to do it in a very simple and concise manner. Anyway we often need to extract various information about the
user after he has been authenticated. Unfortunately this is something that is not standardized and obviously each OAuth
provider manages user data in very specific manner according to its purposes.

So each provider offers specific APIs with specific data schemes to extract data about the authenticated
user.

That's not a big deal if we build apps that adopts a single OAuth provider, but if we want to adopt more of them things
can get really cumbersome.

Just to make things clearer suppose you want to allow users in your app to sign up with Facebook, Twitter and Linkedin.
Probably, to increase conversion rate and speed up the sign up process, you may want to populate the user profile on your
app by copying data from the OAuth provider user profile he used to sign up. Yes, you have to deal with 3 different sets of
APIs and data schemes! And suppose you would be able to add GitHub and Google one day, that will count for 5
different APIs and data schemes... not so maintainable, isn't it?

Ok, relax... this library exists to ease this pain! It provides an abstraction layer on the top library
to extract user data from the OAuth providers you already integrated in your app.

It offers a uniform and (really) simple interface to extract the most interesting and common user data such as *Name*,
*Username*, *Id* and so on.

Just to give you a quick idea of what is possible with the library have a look at the following snippet:

``` php
// $service is an istance of \OAuth\Common\Service\ServiceInterface (eg. the "Facebook" service)
// with a valid access token
$extractor = $service->constructExtractor(); // get the extractor for the given service
echo $extractor->getUniqueId(); // prints out the unique id of the user
echo $extractor->getUsername(); // prints out the username of the user
echo $extractor->getImageUrl(); // prints out the url of the user profile image
```

Mapped fields and methods
-------------------------

The library leverages around the concept of *extractors*. An extractor is a specific implementation of the logic to
retrieve data for a given OAuth provider.

Each extractor can retrieve the following user data fields:

- *uniqueId* (string)
- *username* (string)
- *firstName* (string)
- *lastName* (string)
- *fullName* (string)
- *email* (string)
- *location* (string)
- *description* (string)
- *imageUrl* (string)
- *profileUrl* (string)
- *websites* (array)
- *verifiedEmail* (bool)

For each field you have convenience methods to get the value of the field or to check if it is supported by the given
provider:

- `supportsUniqueId()`
- `getUniqueId()`
- `supportsUsername()`
- `getUsername()`
- `supportsFirstName()`
- `getFirstName()`
- `supportsLastName()`
- `getLastName()`
- `supportsFullName()`
- `getFullName()`
- `supportsEmail()`
- `getEmail()`
- `supportsLocation()`
- `getLocation()`
- `supportsDescription()`
- `getDescription()`
- `supportsImageUrl()`
- `getImageUrl()`
- `supportsProfileUrl()`
- `getProfileUrl()`
- `supportsWebsites()`
- `getWebsites()`
- `supportsVerifiedEmail()`
- `isEmailVerified()`

If you try to get a field that is not supported or it has not been set by the user on its profile you will get a `null`
value.

All the other other data offered by the service API is mapped under a special *extra* array accessible with the following
methods:

- `supportsExtra()`
- `getExtra($key)`
- `getExtras()`

You can have a look at the [ExtractorInterface](src/UserData/Extractor/ExtractorInterface.php) *docblocks* if you want
a better understanding of what every method does.

**NOTE**: In many providers some user data fields are available only if you set the proper scopes/settings for your OAuth app.

Extractor helpers
-----------------
Each extractor has next helpers:

- `saveImage($savePath, /* int|false */ $width, /* int|false */ $height)`, works only if image url available, resize and save image to disk
- `getImageRawData(/* int|false */ $width, /* int|false */ $height)`, works only if image url available, returns raw image data

**NOTE**: Some helpers only works if fields required for it's work are available
