<?php

/**
* Exact API
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

require_once 'ExactOAuth.php';

class ExactApi
{
	
	const METHOD_POST = 'post';
	
	const URL_API = 'https://start.exactonline.%s/api/v1/';
	
	/** @var string */
	protected $countryCode;

	/** @var string */
	protected $clientId;

	/** @var string */
	protected $clientSecret;

	/** @var string */
	protected $refreshToken;
	
	/** @var string */
	protected $accessToken;
	
	/** @var int */
	protected $expiresIn;
	
	/** @var string */
	protected $division;

	/** @var ExactOAuth */
	protected $oAuthClient;
	

	/**
	 * @param string $countryCode
	 * @param string $clientId
	 * @param string $clientSecret
	 * @param string $division
	 * @param string|NULL $refreshToken
	 */
	public function __construct($countryCode, $clientId, $clientSecret, $division, $refreshToken = NULL)
	{
		$this->countryCode = $countryCode;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->refreshToken = $refreshToken;
		$this->division = $division;
	}
	
	/**
	 * @return ExactOAuth
	 */
	public function getOAuthClient()
	{
		if (!$this->oAuthClient) {
			$this->oAuthClient = new ExactOAuth(
				$this->countryCode, $this->clientId, $this->clientSecret
			);
		}
		
		return $this->oAuthClient;
	}
	
	/**
	 * @param string $token
	 */
	public function setRefreshToken($token)
	{
		$this->refreshToken = $token;
		// Majorlabel
		$tokens_json = file_get_contents('store_tokens.json');
		$token_array = json_decode($tokens_json,true);
		$token_array = json_decode($token_array,true);
		$token_array['refresh_token'] = $token;
		file_put_contents('store_tokens.json',$json_array);
		// Majorlabel
	}

	/**
	 * @return string|FALSE
	 * @throws \ErrorException
	 */
	protected function initAccessToken()
	{
		if (empty($this->accessToken) || $this->isExpired()) {
			
			if (empty($this->refreshToken)) {
				throw new \ErrorException('Refresh token is not specified.');
			}
			
			$refreshed =  $this->getOAuthClient()->refreshAccessToken($this->refreshToken);
			if (!$refreshed) {
				return FALSE;
			}
			$this->setExpiresIn($refreshed['expires_in']);
			$this->refreshToken = $refreshed['refresh_token'];
			$this->accessToken = $refreshed['access_token'];
		}
		
		return $this->accessToken;
	}

	/**
	 * @param int $expiresInTime
	 */
	protected function setExpiresIn($expiresInTime)
	{
		$this->expiresIn = time() + $expiresInTime;
	}
	
	/**
	 * @return int
	 */
	protected function isExpired()
	{
		return $this->expiresIn > time();
	}
	
	/**
	 * @param string $resourceUrl
	 * @param array|NULL $params
	 * @return string
	 */
	protected function getRequestUrl($resourceUrl, $params = NULL)
	{
		$resourceUrlParts = parse_url($resourceUrl);
		$baseUrl = sprintf(self::URL_API, $this->countryCode);
		$apiUrl = $baseUrl . $this->division.'/'.$resourceUrlParts['path'];
		
		if (isset($resourceUrlParts['query'])) {
			$apiUrl .= '?' . $resourceUrlParts['query'];
		} else
		if ($params && is_array($params)) {
			$apiUrl .= '?' . http_build_query($params, '', '&');
		}
		
		return $apiUrl;
	}
	
	/**
	 * @param string $url
	 * @param string $method
	 * @param array|NULL $payload
	 * @return string
	 */
	public function sendRequest($url, $method, $payload = NULL)
	{
		if ($payload && !is_array($payload)) {
			throw new \ErrorException('Payload is not valid.');
		}
		
		if (!$accessToken = $this->initAccessToken()) {
			throw new \ErrorException('Access token was not initialized');
		}
		
		$requestUrl = $this->getRequestUrl($url, array(
		    'access_token' => $accessToken
		));
		
		// Base cURL option
		$curlOpt = array();
		$curlOpt[CURLOPT_URL] = $requestUrl;
		$curlOpt[CURLOPT_RETURNTRANSFER] = TRUE;
		$curlOpt[CURLOPT_SSL_VERIFYPEER] = TRUE;
		$curlOpt[CURLOPT_HEADER] = false;
			
		if ($method == self::METHOD_POST) {
			
			$curlOpt[CURLOPT_HTTPHEADER] = array(
			    'Content-Type:application/json', 
			    'access_token:' . $accessToken, 
			    'Content-length: ' . strlen(json_encode($payload))
			);
			$curlOpt[CURLOPT_POSTFIELDS] = json_encode($payload);
			$curlOpt[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
		}
		
		$curlHandle = curl_init();
		curl_setopt_array($curlHandle, $curlOpt);
		
		return curl_exec($curlHandle);
	}

}
