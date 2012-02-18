<?php

use Metrics\Client;

/**
 * Description of ClientTest
 *
 * @author ole
 */
class ClientTest extends \PHPUnit_Framework_TestCase {

	public function testSay() {
		$client = new Client();
		$var = 'string';
		$this->assertEquals($var, $client->say($var));
	}

}