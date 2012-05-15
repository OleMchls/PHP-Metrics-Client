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

	/**
	 * Stores http client/transport.
	 *
	 * @var Buzz\Client\AbstractClient
	 */
	protected $transport = null;

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
		$request = new Request();
		$response = new Response();
		$transport = $this->getTransport();

		$request->setMethod($method);
		$request->setResource($this->buildPath($path));
		$request->setHost(self::URI);
		$request->addHeader('Authorization: Basic ' . base64_encode($this->getAuthCredentials()));
		$request->addHeader('User-Agent: ' . $this->getUserAgent());

		if (count($data)) {
			$request->addHeader('Content-Type: application/json');
			$request->setContent(json_encode($data));
		}

		$transport->send($request, $response);

		return json_decode($response->getContent());
	}

	/**
	 * Gets the transport/client.
	 *
	 * @return Buzz\Client\AbstractClient
	 */
	protected function getTransport() {
		if ($this->transport === null) {
			$this->transport = new Curl();
		}
		return $this->transport;
	}

	/**
	 * Sets the transport/client for sending the request to metrics.
	 *
	 * @param Buzz\Client\AbstractClient $transport
	 *
	 * @return void
	 */
	public function setTransport(\Buzz\Client\ClientInterface $transport) {
		$this->transport = $transport;
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
		return sprintf("php-librato-metrics-v01");
	}

	/**
	 * Constructs auth credentials.
	 *
	 * @return string
	 */
	protected function getAuthCredentials() {
		return ($this->email . ':' . $this->token);
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