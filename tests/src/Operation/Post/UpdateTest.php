<?php
/**
 * Class Operation\Post\UpdateTest
 *
 * Tests for class Lark\Operation\Post\Update
 *
 * @package Lark
 */
namespace Operation\Post;

use Operation\BaseTestCase;
use \Lark\Operation\Post\Update;

class UpdateTest extends BaseTestCase {

	/**
	 * The ID of the post we create and then test updating
	 * @var int
	 */
	private $post_id;

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return Update::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		return [
			'id' => 0, // generated during setUp
			'post' => [
				'post_title' => 'New Updated Title',
				'post_content' => '<p>New updated content.</p>',
			],
			'name' => 'updated_post_id',
		];
	}

	public function setUp() {
		parent::setUp();

		// Create the post that we will update during our test and add it to `details`
		$this->post_id = $this->factory()->post->create();
		$this->details['id'] = $this->post_id;
	}

	/**
	 * @covers \Lark\Operation\Post\Update::execute()
	 *
	 * @test
	 */
	public function testExecute() {

		$this->operation->execute( $this->details );

		$this->assertSame( $this->post_id, $this->transaction->getTransactionValue( 'updated_post_id' ) );

		$post = get_post( $this->post_id );

		$this->assertSame( 'New Updated Title', $post->post_title );
		$this->assertSame( '<p>New updated content.</p>', $post->post_content );
	}
}
