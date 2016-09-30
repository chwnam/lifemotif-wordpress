<?php

function lm_lifemotif_callback() {

	wp_enqueue_style(
		'lm-ui-datepicker',
		plugin_dir_url( LMWP_MAIN ) . 'assets/css/jquery-ui-themes-1.11.4/themes/base/jquery-ui.min.css',
		array(),
		'1.11.4'
	);

	wp_enqueue_script( 'jquery-ui-datepicker' );

	wp_register_script(
		'lm-datepicker',
		plugin_dir_url( LMWP_MAIN ) . 'assets/js/lm-datepicker.js',
		array( 'jquery', 'jquery-ui-datepicker' ),
		FALSE,
		TRUE
	);

	$timezone = get_option( 'timezone_string' );
	$now      = date_create( NULL, new DateTimeZone( $timezone ) );

	// 이번 달의 포스트 목록
	$query = new WP_Query(
		array(
			'post_type'   => 'lifemotif-article',
			'post_status' => array( 'draft', 'publish' ),
			'order'       => 'ASC',
			'orderby'     => 'date',
			'm'           => $now->format( 'Ym' ),
			'nopaging'    => TRUE,
		)
	);

	$articles = array();
	foreach ( $query->posts as $post ) {
		$date = date_create_from_format( 'Y-m-d H:i:s', $post->post_date );

		$articles[ intval( $date->format( 'd' ) ) ] = $post->ID;
	}

	wp_localize_script(
		'lm-datepicker',
		'dpObject',
		array(
			'firstArticleDate' => get_option( 'lm-first-article-date' ),
			'articles'         => $articles,
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'lm-datepicker-nonce' ),
		)
	);

	wp_enqueue_script( 'lm-datepicker' );

	ob_start();
	wp_localize_jquery_ui_datepicker();
	lm_get_template( 'lifemotif.php', array() );

	return ob_get_clean();
}

add_shortcode( 'lifemotif', 'lm_lifemotif_callback' );
