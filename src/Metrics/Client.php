<?php

namespace Metrics;

use Buzz\Message\Request;
use Buzz\Message\Response;
use Buzz\Client\Curl;

/**
 * Client for sending and recieving data from librato Metrics.
 *
 * @author Ole 'nesQuick' Michaelis <Ole.Michaelis@googlemail.com>
 */
class Client {

	/**
	 * Stores Metrics mail, needed for auth.
	 *
	 * @var string
	 */
	protected $email;

	/**
	 * Stores Metrics token, needed for auth.
	 *
	 * @var string
	 */
	protected $token;

	const URI = 'https://metrics-api.librato.com';
	const API_VERSION = 'v1';

	/**
	 * Sets needed Auth information.
	 *
	 * @param string $email
	 * @param string $token
	 */
	public function __construct($email, $token) {
		$this->email = $email;
		$this->token = $token;
	}

	/**
	 * Helper to send requests to Metrics API.
	 *
	 * @param string $path Path after metrics api version.
	 * @param string $method HTTP Mthod, 'GET' or 'POST'.
	 * @param array<string,array> $data Metrics data.
	 *
	 * @return stdClass
	 */
	protected function request($path, $method, array $data = array()) {
		$request = new Request($method, $this->buildPath($path), self::URI);
		$response = new Response();
		$client = new Curl();

		$request->addHeader('Authorization: Basic ' . base64_encode($this->email . ':' . $this->token));
		$request->addHeader('User-Agent: ' . $this->getUserAgent());

		if (count($data)) {
			$request->addHeader('Content-Type: application/json');
			$request->setContent(json_encode($data));
		}

		$client->send($request, $response);

		return json_decode($response->getContent());
	}

	/**
	 * Helper to build path on Metrics API.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	protected function buildPath($path) {
		return '/' . self::API_VERSION . $path;
	}

	/**
	 * Returns user agent to identify libary.
	 *
	 * @return sting
	 */
	protected function getUserAgent() {
		return sprintf("librato-metrics/%s (PHP %s)", self::API_VERSION, PHP_VERSION);
	}

	/**
	 * Fetches data from Metrics API.
	 *
	 * @param string $path Path on Metrics API to request. For Example '/metrics/'.
	 *
	 * @return stdClass
	 */
	public function get($path) {
		return $this->request($path, Request::METHOD_GET);
	}

	/**
	 * Posts data to Metrics.
	 *
	 * @param string $path Path on Metrics API to request. For Example '/metrics'.
	 * @param array<string,array> $data
	 *
	 * @return stdClass
	 */
	public function post($path, array $data) {
		return $this->request($path, Request::METHOD_POST, $data);
	}

}