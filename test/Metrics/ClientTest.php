<?php

use Metrics\Client;

/**
 * Description of ClientTest
 *
 * @author ole
 */
class ClientTest extends \PHPUnit_Framework_TestCase {

	protected $data = array(
		'gauges' => array(
			array(
				'name' => 'php-librato-metrics-test',
				'value' => 123
			)
		)
	);

	protected function buildClient() {
		return new Client($_ENV['metrics_email'], $_ENV['metrics_token']);
	}

	public function testPostingDataT0Metrics() {
		$client = $this->buildClient();
		$response = $client->post('/metrics', $this->data);
		$this->assertNull($response);
	}

	public function testGettingDataFromMetrics() {
		$client = $this->buildClient();
		$client->post('/metrics', $this->data);
		$metrics = $this->data['gauges'][0];
		$response = $client->get('/metrics/' . $metrics['name']);
		$this->assertEquals($response->name, $metrics['name']);
	}

}