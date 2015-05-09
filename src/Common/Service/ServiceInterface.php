<?php

namespace OAuth\Common\Service;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Url;
use OAuth\UserData\Extractor\ExtractorInterface;
use OAuth\UserData\ExtractorFactoryInterface;

/**
 * Defines methods common among all OAuth services.
 */
interface ServiceInterface {

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (service-specific) will be used.
	 *
	 * @param string|Url $path
	 * @param array|string $body Request body if applicable (an associative array will automatically be converted into a urlencoded body)
	 * @param string $method HTTP method
	 *
	 * @param array $extraHeaders Extra headers if applicable. These will override service-specific defaults.
	 * @return string
	 */
	public function request($path, $body = [], $method = 'GET', array $extraHeaders = []);

	/**
	 * Shortcut for json_decode($this->request(...
	 *
	 * @param $uri
	 * @param array|string $body
	 * @param string $method
	 * @param array $extraHeaders
	 * @return array
	 */
	public function requestJSON($uri, $body = [], $method = 'GET', array $extraHeaders = []);

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
	 *
	 * @param Url|string $uri
	 * @param array|string $body Request body if applicable
	 * @param array $headers Extra headers if applicable.
	 * @param string $method HTTP method
	 * @throws TokenResponseException
	 * @return string
	 */
	public function httpRequest($uri, $body = [], array $headers = [], $method = 'POST');

	/**
	 * Returns the url to redirect to for authorization purposes.
	 *
	 * @param array $additionalParameters
	 * @return Url
	 */
	public function getAuthorizationUri(array $additionalParameters = []);

	/**
	 * Returns the authorization API endpoint.
	 *
	 * @return Url
	 */
	public function getAuthorizationEndpoint();

	/**
	 * Returns the access token API endpoint.
	 *
	 * @return Url
	 */
	public function getAccessTokenEndpoint();

	/**
	 * Get Extractor for service
	 *
	 * @param ExtractorFactoryInterface $extractorFactory
	 * @return ExtractorInterface
	 */
	public function constructExtractor(ExtractorFactoryInterface $extractorFactory = NULL);

	/**
	 * Redirect user to authorization uri
	 *
	 * @return $this
	 */
	public function redirectToAuthorizationUri();
}
