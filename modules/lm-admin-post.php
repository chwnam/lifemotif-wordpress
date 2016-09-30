<?php

add_action( 'admin_post_update-archive', 'lm_update_archive' );
add_action( 'admin_post_update-mapping', 'lm_update_mapping' );

function lm_update_archive() {

	lm_verify_nonce( lm_POST( 'wp-nonce' ), 'lmwp-nonce-update-archive' );

	require_once LMWP_PATH . '/modules/lm-diem-wrapper-factory.php';

	$diem   = LM_Diem_Wrapper_Factory::get_diem_wrapper();
	$result = $diem->fetch_incrementally();

	if ( $result ) {
		update_option( 'lm-diem-last-output', $result );
	}

	// redirec to admin
	$redirect_uri = lm_POST( 'redirect-uri', 'esc_url_raw' );
	if ( $redirect_uri ) {
		wp_redirect( $redirect_uri );
	} else {
		die( 0 );
	}
}

function lm_update_mapping() {

	lm_verify_nonce( lm_POST( 'wp-nonce' ), 'lmwp-nonce-update-mapping' );

	// mapping process
	require_once LMWP_PATH . '/libs/lm-post-mapper.php';

	$last_output = get_option( 'lm-mapper-last-output', array() );
	$latest_mid  = lm_get_from_assoc( $last_output, 'last_mid', 'intval', 0 );

	$mapper = new LM_Post_Mapper();
	$output = $mapper->do_mapping( $latest_mid );

	update_option( 'lm-mapper-last-output', $output );

	// update minimum date
	$query = new WP_Query(
		array(
			'post_type'      => 'lifemotif-article',
			'order'          => 'ASC',
			'orderby'        => 'date',
			'cache_results'  => FALSE,
			'posts_per_page' => 1,
			'offset'         => 0,
		)
	);
	if ( $query->posts ) {
		$post     = $query->posts[0];
		$datetime = DateTime::createFromFormat( 'Y-m-d H:i:s', $post->post_date );

		update_option( 'lm-first-article-date', $datetime->format( 'Y-m-d' ) );
	}

	// redirect to admin
	$redirect_uri = lm_POST( 'redirect-uri', 'esc_url_raw' );
	if ( $redirect_uri ) {
		wp_redirect( $redirect_uri );
	} else {
		die( 0 );
	}
}
