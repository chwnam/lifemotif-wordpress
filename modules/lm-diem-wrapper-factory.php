<?php

class LM_Diem_Wrapper_Factory {

	/** @var LM_Diem_Wrapper */
	private static $diem_wrapper = NULL;

	public static function get_diem_wrapper() {

		if ( ! static::$diem_wrapper ) {

			$profile_path = get_option( 'lm-profile-path' );
			$python_path  = get_option( 'lm-python-path' );
			$diem_path    = get_option( 'lm-diem-path' );
			$log_level    = get_option( 'lm-log-level' );
			$log_file     = get_option( 'lm-log-file' );

			static::$diem_wrapper = new LM_Diem_Wrapper(
				$python_path, $diem_path, $profile_path, $log_level, $log_file );
		}

		return static::$diem_wrapper;
	}
}
