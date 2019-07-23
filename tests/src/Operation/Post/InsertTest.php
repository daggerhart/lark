<?php
/**
 * Class Operation\Post\InsertTest
 *
 * Tests for class Lark\Operation\Post\Insert
 *
 * @package Lark
 */
namespace Operation\Post;

use Operation\BaseTestCase;
use \Lark\Operation\Post\Insert;

class InsertTest extends BaseTestCase {

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return Insert::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		return [
			'post' => [ 'post_title' => 'Example Post 123' ],
			'name' => 'new_post_id',
		];
	}

	/**
	 * @covers \Lark\Operation\Post\Insert::execute()
	 *
	 * @test
	 */
	public function testExecute() {

		$this->operation->execute( $this->details );

		$inserted_post = get_page_by_title( 'Example Post 123', 'OBJECT', 'post' );
		$this->assertSame( 'Example Post 123', $inserted_post->post_title );
		$this->assertSame( $inserted_post->ID, $this->transaction->getTransactionValue( 'new_post_id' ) );
	}
}
