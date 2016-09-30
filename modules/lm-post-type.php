<?php

function lmwp_add_post_type_lifemotif_article() {

	$args = array(
		'labels' => array(
			'name'          => __( 'Diaries', 'lifemotif' ),
			'singular_name' => __( 'Diary', 'lifemotif' ),
		),

		'description'         => 'A custom post for my everyday diary, lifemotif article.',
		'public'              => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'has_archive'         => true,

		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'show_in_menu'      => 'lm-admin-page',
		'show_in_admin_bar' => true,

		'hierarchical' => false,
		'supports'     => false,

		'can_export' => false,

	);

	register_post_type( 'lifemotif-article', $args );
}

add_action( 'init', 'lmwp_add_post_type_lifemotif_article' );
