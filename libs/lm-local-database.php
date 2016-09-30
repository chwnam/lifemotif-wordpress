<?php
require_once( 'lm-singleton.php' );
require_once( 'lm-diem-profile.php' );


class LM_Local_Database extends LM_Singleton {

	private $handler = NULL;

	protected function __construct() {

		$db_path = LM_Diem_Profile::get_db_path();

		if ( $db_path ) {

			$this->handler = new SQLite3( $db_path, SQLITE3_OPEN_READONLY );

			$callback = array( $this, 'close_db' );
			if ( ! has_action( 'shutdown', $callback ) ) {
				add_action( 'shutdown', $callback );
			}
		}
	}

	public function close_db() {

		if ( $this->handler ) {
			$this->handler->close();
			$this->handler = NULL;
		}
	}

	public static function get_handler() {

		$instance = static::get_instance();

		return $instance->handler;
	}
}