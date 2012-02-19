<?php

namespace Metrics;

use Buzz\Message\Request;
use Buzz\Message\Response;
use Buzz\Client\Curl;

/**
 * Description of Client
 *
 * @author ole
 */
class Client {

	protected $email;
	protected $token;

	const URI = 'https://metrics-api.librato.com';
	const API_VERSION = 'v1';

	public function __construct($email, $token) {
		$this->email = $email;
		$this->token = $token;
	}

	protected function request($path, $method, array $data = array()) {
		$request = new Request($method, $this->buildPath($path), self::URI);
		$response = new Response();
		$client = new Curl();

		$request->addHeader('Authorization: Basic ' . base64_encode($this->email . ':' . $this->token));
		if (count($data)) {
			$request->addHeader('Content-Type: application/json');
			$request->setContent(json_encode($data));
		}

		$client->send($request, $response);

		return json_decode($response->getContent());
	}

	protected function buildPath($path) {
		return '/' . self::API_VERSION . $path;
	}

	public function get($path) {
		return $this->request($path, Request::METHOD_GET);
	}

	public function post($path, array $data) {
		return $this->request($path, Request::METHOD_POST, $data);
	}

}