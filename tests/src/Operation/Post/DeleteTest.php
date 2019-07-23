<?php
/**
 * Class Operation\Post\DeleteTest
 *
 * Tests for class Lark\Operation\Post\Delete
 *
 * @package Lark
 */
namespace Operation\Post;

use Operation\BaseTestCase;
use \Lark\Operation\Post\Delete;

class DeleteTest extends BaseTestCase {

	/**
	 * The ID of the post we create and then test deleting
	 * @var int
	 */
	private $post_id;

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return Delete::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		return [
			'id' => 0, // generated during setUp
			'name' => 'delete_post_result',
		];
	}

	public function setUp() {
		parent::setUp();

		// Create the post that we will delete during our test and add it to `details`
		$this->post_id = $this->factory()->post->create();
		$this->details['id'] = $this->post_id;
	}

	/**
	 * @covers \Lark\Operation\Post\Delete::execute()
	 *
	 * @test
	 */
	public function testExecute() {
		# Verify the post exists beforehand
		$this->assertNotNull( get_post( $this->post_id ) );

		$this->operation->execute( $this->details );

		# Without specifying otherwise, the post should land in the trash
		$this->assertSame( 'trash', get_post( $this->post_id )->post_status );
		# The result of deletion is the post object itself
		$this->assertSame( $this->post_id, $this->transaction->getTransactionValue('delete_post_result')->ID );

		# If we execute again, the post should get deleted altogether
		$this->operation->execute( $this->details );

		$this->assertNull( get_post( $this->post_id ) );
		# The result of deletion is the post object itself
		$this->assertSame( $this->post_id, $this->transaction->getTransactionValue('delete_post_result')->ID );
	}

	/**
	 * Test force deletion which bypasses trash
	 * @covers \Lark\Operation\Post\Delete::execute()
	 */
	public function testExecuteForce() {
		# Verify the post exists beforehand
		$this->assertNotNull( get_post( $this->post_id ) );

		$this->details['force_delete'] = true;

		$this->operation->execute( $this->details );

		$this->assertNull( get_post( $this->post_id ) );
		# The result of deletion is the post object itself
		$this->assertSame( $this->post_id, $this->transaction->getTransactionValue('delete_post_result')->ID );
	}
}
