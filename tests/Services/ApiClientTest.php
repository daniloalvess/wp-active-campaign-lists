<?php
namespace WpActiveCampaignLists\Tests\Services;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use WpActiveCampaignLists\Services\ApiClient;

class ApiClientTest extends TestCase {

	use MockeryPHPUnitIntegration;

	public function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		$this->client = new ApiClient( 'https://test.com', 'xxxxxx');
	}

	public function testShouldReturnValidHeaders() {
		$headers = $this->client->get_headers();
		$keys    = ['Api-Token', 'Content-Type'];

		$this->assertIsArray( $headers );
		$this->assertCount( 2, $headers );
		$this->assertEquals( $keys, array_keys( $headers ) );
		$this->assertEquals( $headers['Api-Token'], 'xxxxxx');
	}

	public function testShouldReturnResourceUrl() {
		$this->assertEquals( 'https://test.com/api/3/example', $this->client->get_url( 'example' ) );
	}

	public function testShouldBeValidCredencials() {
		$is_invalid = $this->client->is_invalid_credencials();

		$this->assertIsBool( $is_invalid );
		$this->assertEquals( false, $is_invalid );
	}

	public function testShouldBeInvalidCredencials() {
		$client     = new ApiClient( '', '' );
		$is_invalid = $client->is_invalid_credencials();

		$this->assertIsBool( $is_invalid );
		$this->assertEquals( true, $is_invalid );
	}

	public function testShouldReturnValidBody() {
		$sample_response = ['body' => json_encode(['test' => 'Sample response'])];

		$body = $this->client->get_body( $sample_response );

		$this->assertIsObject( $body );
		$this->assertObjectNotHasAttribute( 'error', $body );
	}

	public function testShouldReturnErrorBody() {
		$wp_error = \Mockery::mock( '\WP_Error' );
		$wp_error->shouldReceive( 'get_error_message' )
			->andReturn( 'Sample error message' );

		$body = $this->client->get_body( $wp_error );

		$this->assertIsObject( $body );
		$this->assertObjectHasAttribute( 'success', $body );
		$this->assertObjectHasAttribute( 'error', $body );
		$this->assertEquals( 'Sample error message', $body->error );
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
