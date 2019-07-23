<?php
/**
 * Class Operation\Post\GetAllTest
 *
 * Tests for class Lark\Operation\Post\GetAll
 *
 * @package Lark
 */
namespace Operation\Post;

use Operation\BaseTestCase;

use \Lark\Operation\Post\GetAll;

class GetAllTest extends BaseTestCase {

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return GetAll::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		return [
			'args' => [
				'post_type' => 'page',
				'orderby' => 'title',
				'order' => 'ASC',
			],
			'name' => 'found_pages',
		];
	}

	/**
	 * @covers \Lark\Operation\Post\GetAll::execute()
	 *
	 * @test
	 */
	public function testExecute() {

		$this->factory()->post->create(['post_type' => 'page', 'post_title' => 'Because']);
		$this->factory()->post->create(['post_type' => 'page', 'post_title' => 'Again']);
		$this->factory()->post->create(['post_type' => 'page', 'post_title' => 'Concepts']);

		$this->operation->execute( $this->details );
		$result = $this->transaction->getTransactionValue( $this->details['name'] );

		$this->assertCount( 3, $result );
		$this->assertSame( 'Again', $result[0]->post_title );
	}
}