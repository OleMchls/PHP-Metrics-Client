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