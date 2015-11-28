<?php

/**
* Exact oAauth
* Copyright (c) iWebDevelopment B.V. (https://www.iwebdevelopment.nl)
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) iWebDevelopment B.V. (https://www.iwebdevelopment.nl)
* @link          https://www.iwebdevelopment.nl
* @since         01-06-2015
* @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/

class ExactOAuth
{
	
	const URL_AUTH = 'https://start.exactonline.%s/api/oauth2/auth';
	const URL_TOKEN = 'https://start.exactonline.%s/api/oauth2/token';
	
	const GRANT_AUTHORIZATION_CODE = 'authorization_code';
	const GRANT_REFRESH_TOKEN = 'refresh_token';
	
	const RESPONSE_TYPE_CODE = 'code';
	
	/** @var string */
	public $clientId;

	/** @var string */
	public $clientSecret;

	/** @var string */
	public $countryCode;
	
	/** @var string */
	public $redirectUri;

	/**
	 * @param string $countryCode
	 * @param string $clientId
	 * @param string $clientSecret
	 */
	public function __construct($countryCode, $clientId, $clientSecret)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->countryCode = $countryCode;
	}

	/**
	 * @param string|NULL $redirectUri
	 * @param string $responseType
	 * @return string
	 * @throws \ErrorException
	 */
	public function getAuthenticationUrl($redirectUri = NULL, $responseType = self::RESPONSE_TYPE_CODE)
	{
		if (empty($this->redirectUri) && empty($redirectUri)) {
			throw new \ErrorException('Redirect Uri is not specified.');
		}
		
		$params = array(
		    'client_id' => $this->clientId,
		    'redirect_uri' => $redirectUri ? $redirectUri : $this->redirectUri,
		    'response_type' => $responseType
		);

		$url = sprintf(self::URL_AUTH, $this->countryCode);

		return $url . '?' . http_build_query($params, '', '&');
	}

	/**
	 * @param string $code
	 * @param string|NULL $redirectUri
	 * @param string $grantType
	 * @return array {access_token, token_type, expires_in, refresh_token}
	 * @throws \ErrorException
	 */
	public function getAccessToken($code, $redirectUri = NULL, $grantType = self::GRANT_AUTHORIZATION_CODE)
	{
		if (empty($this->redirectUri) && empty($redirectUri)) {
			throw new \ErrorException('Redirect Uri is not specified.');
		}
		
		$params = array(
		    'code' => $code,
		    'client_id' => $this->clientId,
		    'grant_type' => $grantType,
		    'client_secret' => $this->clientSecret,
		    'redirect_uri' => $redirectUri ? $redirectUri : $this->redirectUri,
		);

		$url = sprintf(self::URL_TOKEN, $this->countryCode);

		return $this->getResponse($url, $params);
	}

	/**
	 * @param string $refreshToken
	 * @return array {access_token, expires_in, refresh_token}
	 */
	public function refreshAccessToken($refreshToken)
	{
		$params = array(
		    'refresh_token' => $refreshToken,
		    'grant_type' => self::GRANT_REFRESH_TOKEN,
		    'client_id' => $this->clientId,
		    'client_secret' => $this->clientSecret
		);
		
		$url = sprintf(self::URL_TOKEN, $this->countryCode);
		
		return $this->getResponse($url, $params);
	}

	/**
	 * @param string $url
	 * @param array $params
	 * @return array|NULL
	 */
	public function getResponse($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
		$result = curl_exec($ch);

		$decodedResult = json_decode($result, TRUE);
		
		if (isset($decodedResult['error'])) {
			return FALSE;
		}
		
		return $decodedResult;
	}
	
	/**
	 * @param string $uri
	 */
	public function setRedirectUri($uri)
	{
		$this->redirectUri = $uri;
	}

}