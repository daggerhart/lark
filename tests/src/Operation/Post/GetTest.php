<?php
/**
 * Class Operation\Post\GetTest
 *
 * Tests for class Lark\Operation\Post\Get
 *
 * @package Lark
 */
namespace Operation\Post;

use Operation\BaseTestCase;

use \Lark\Operation\Post\Get;

class GetTest extends BaseTestCase {

	/**
	 * The ID of the post we create and then test getting
	 * @var int
	 */
	private $post_id;

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return Get::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		return [
			'id' => 0, // generated during setUp
			'name' => 'found_post_object',
		];
	}

	public function setUp() {
		parent::setUp();

		// Create the post that we will get during our test and add it to `details`
		$this->post_id = $this->factory()->post->create();
		$this->details['id'] = $this->post_id;
	}

	/**
	 * @covers \Lark\Operation\Post\Get::execute()
	 *
	 * @test
	 */
	public function testExecute() {
		$this->operation->execute( $this->details );
		$this->assertSame( $this->post_id, $this->transaction->getTransactionValue( 'found_post_object' )->ID );
	}
}
