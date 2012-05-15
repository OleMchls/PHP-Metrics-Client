<?php

use Metrics\Client;

/**
 * Tests for Metrics Client.
 *
 * @author Ole 'nesQuick' Michaelis <Ole.Michaelis@googlemail.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase {

	protected function buildClient() {
		return new Client($_ENV['metrics_email'], $_ENV['metrics_token']);
	}

	public function gaugesProvider() {
		return array(
			array(// 1. Run
				array(// 1. Param
					'gauges' => array(
						array(
							'name' => 'php-librato-metrics-test',
							'value' => mt_rand(100, 1337)
						)
					)
				)
			),
			array(// 2. Run
				array(// 1. Param
					'gauges' => array(
						array(
							'name' => 'php-librato-metrics-test',
							'value' => mt_rand(100, 1337)
						)
					)
				)
			)
		);
	}

	public function testGetTransportDefault() {
		$getTransport = new ReflectionMethod('\Metrics\Client', 'getTransport');
		$getTransport->setAccessible(true);

		$this->assertInstanceOf('\Buzz\Client\Curl', $getTransport->invoke($this->buildClient()));
	}

	public function testSetTransport() {
		$client = $this->buildClient();
		$transport = new \Buzz\Client\FileGetContents();
		$client->setTransport($transport);

		$getTransport = new ReflectionMethod('\Metrics\Client', 'getTransport');
		$getTransport->setAccessible(true);

		$this->assertSame($transport, $getTransport->invoke($client));
	}

	public function testBuildPath() {
		$buildPath = new ReflectionMethod('\Metrics\Client', 'buildPath');
		$buildPath->setAccessible(true);

		$this->assertEquals('/v1/test-path', $buildPath->invoke($this->buildClient(), '/test-path'));
	}

	public function testGetUserAgent() {
		$getUserAgent = new ReflectionMethod('\Metrics\Client', 'getUserAgent');
		$getUserAgent->setAccessible(true);

		$this->assertEquals('php-librato-metrics-v01', $getUserAgent->invoke($this->buildClient()));
	}

	public function testGetAuthCredentials() {
		$getAuthCredentials = new ReflectionMethod('\Metrics\Client', 'getAuthCredentials');
		$getAuthCredentials->setAccessible(true);

		$this->assertEquals($_ENV['metrics_email'] . ':' . $_ENV['metrics_token'], $getAuthCredentials->invoke($this->buildClient()));
	}

	/**
	 * @dataProvider gaugesProvider
	 */
	public function testPostingDataT0Metrics($metrics) {
		if ($_ENV['metrics_token'] == '...') {
			$this->markTestSkipped('No valid auth credentials provided');
		}
		$client = $this->buildClient();
		$response = $client->post('/metrics', $metrics);
		$this->assertNull($response);
	}

	/**
	 * @dataProvider gaugesProvider
	 */
	public function testGettingDataFromMetrics($metrics) {
		if ($_ENV['metrics_token'] == '...') {
			$this->markTestSkipped('No valid auth credentials provided');
		}
		$client = $this->buildClient();
		$client->post('/metrics', $metrics);
		$metric = $metrics['gauges'][0];
		$response = $client->get('/metrics/' . $metric['name']);
		$this->assertEquals($response->name, $metric['name']);
	}

}