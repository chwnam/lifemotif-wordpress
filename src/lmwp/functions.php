<?php
namespace lmwp;

function getFromAssoc( array &$var, $key, $sanitize = '', $default = '' ) {

	$v = $default;

	if ( isset( $var[ $key ] ) ) {
		$v = $var[ $key ];
	}

	if ( is_callable( $sanitize ) ) {
		$v = call_user_func( $sanitize, $v );
	}

	return $v;
}


function GET( $key, $sanitize = '', $default = '' ) {

	return getFromAssoc( $_GET, $key, $sanitize, $default );
}


function POST( $key, $sanitize = '', $default = '' ) {

	return getFromAssoc( $_POST, $key, $sanitize, $default );
}


function REQUEST( $key, $sanitize = '', $default = '' ) {

	return getFromAssoc( $_REQUEST, $key, $sanitize, $default );
}


function getTemplate( $template_name, array $args, $path = LMWP_TEMPLATES ) {

	if ( empty( $template_name ) || ! is_string( $template_name ) ) {
		return;
	}

	$template_name = ltrim( $template_name, '/' );

	if ( ! empty( $args ) ) {
		extract( $args );
	}

	/** @noinspection PhpIncludeInspection */
	include( $path . '/' . $template_name . '.php' );
}


function verifyNonce( $nonce, $action ) {
	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		wp_die( 'nonce verification failed!' );
	}
}


function getAssetsUrl()
{
    return \plugin_dir_url(LMWP_MAIN) . 'src/lmwp/assets';
}
