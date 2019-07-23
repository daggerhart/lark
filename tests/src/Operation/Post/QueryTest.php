<?php
/**
 * Class Operation\QueryTest
 *
 * Tests for class Lark\Operation\Post\Query
 *
 * @package Lark
 */

namespace Operation\Post;

use Operation\BaseTestCase;

use \Lark\Operation\Post\Query;

class QueryTest extends BaseTestCase {

	/**
	 * {@inheritdoc}
	 */
	function className() {
		return Query::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function operationDetails() {
		return [
			'args' => [
				'meta_query' => [
					'relation' => 'AND',
					// Meta key/value that we want to match
					[
						'key' => 'green_flag',
						'value' => 'target_value',
						'compare' => '=',
					],
					// Meta key/value that we want to filter out
					[
						'key' => 'red_flag',
						'value' => 'undesired_value',
						'compare' => '!=',
					],
				],
			],
			'name' => 'query_result',
		];
	}

	/**
	 * @covers \Lark\Operation\Post\Query::execute()
	 *
	 * @test
	 */
	public function testExecute() {

		// This one should not get found even though it has the target value, since it has the undesired value
		$post_id_1 = $this->factory()->post->create();
		update_post_meta( $post_id_1, 'green_flag', 'target_value' );
		update_post_meta( $post_id_1, 'red_flag', 'undesired_value' );

		// This one should get found
		$post_id_2 = $this->factory()->post->create(['post_title' => 'Found Post']);
		update_post_meta( $post_id_2, 'green_flag', 'target_value' );
		update_post_meta( $post_id_2, 'red_flag', 'some_other_value' );

		// This one should not get found
		$post_id_3 = $this->factory()->post->create();
		update_post_meta( $post_id_3, 'green_flag', 'non_target_value' );

		$this->operation->execute( $this->details );

		$result = $this->transaction->getTransactionValue( $this->details['name'] );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Found Post', $result[0]->post_title );
	}
}
