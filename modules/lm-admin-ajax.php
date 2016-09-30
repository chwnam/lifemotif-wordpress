<?php

add_action( 'wp_ajax_get-article-catalog', 'lm_get_article_catalog' );
add_action( 'wp_ajax_get-article', 'lm_get_article' );


function lm_get_article_catalog() {

	lm_verify_nonce( lm_GET( 'wp-nonce' ), 'lm-datepicker-nonce' );

	$year  = lm_GET( 'year', 'intval', 0 );
	$month = lm_GET( 'month', 'intval', 0 );

	if ( $year && $month ) {

		// 이번 달의 포스트 목록
		$query = new WP_Query(
			array(
				'post_type'   => 'lifemotif-article',
				'post_status' => array( 'draft', 'publish' ),
				'order'       => 'ASC',
				'orderby'     => 'date',
				'm'           => sprintf( '%02d%02d', $year, $month ),
				'nopaging'    => TRUE,
			)
		);

		$articles = array();
		foreach ( $query->posts as $post ) {
			$date = date_create_from_format( 'Y-m-d H:i:s', $post->post_date );

			$articles[ intval( $date->format( 'd' ) ) ] = $post->ID;
		}

		wp_send_json_success( $articles );
	}

	wp_send_json_error();

	die();
}


function lm_get_article() {

	lm_verify_nonce( lm_GET( 'wp-nonce' ), 'lm-datepicker-nonce' );

	$post_id = lm_GET( 'post-id', 'intval', 0 );

	$importer = new LM_Diary_Importer();
	$importer->import( $post_id );

	$post = WP_Post::get_instance( $post_id );
	if ( ! $post ) {
		wp_send_json_error();
	}

	wp_send_json_success(
		array(
			'post' => $post,
		)
	);

	die();
}