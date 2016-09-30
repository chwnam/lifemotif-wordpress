<?php
require_once( 'lm-singleton.php' );


class LM_Diem_Profile extends LM_Singleton {

	private $profile = NULL;

	protected function __construct() {

		parent::__construct();

		$this->profile = static::get_diem_profile();
	}

	private static function get_diem_profile() {

		$profile_path = static::get_profile_path();
		if ( ! file_exists( $profile_path ) ) {
			error_log( __METHOD__ . ': profile path is invalid.' );

			return FALSE;
		}

		$profile = json_decode( file_get_contents( $profile_path ), TRUE );
		if ( ! $profile ) {
			error_log( __METHOD__ . ': profile json decode failed.' );

			return FALSE;
		}

		return $profile;
	}

	public static function get_profile_path() {

		return get_option( 'lm-profile-path' );
	}

	public static function get_profile() {

		$instance = static::get_instance();

		if( is_array( $instance->profile ) )  {

			return $instance->profile;
		}

		return FALSE;
	}

	public static function get_db_path() {

		$instance = static::get_instance();

		if( is_array( $instance->profile ) && isset( $instance->profile['database'] ) ) {

			return $instance->profile['database'];
		}

		return FALSE;
	}
}
