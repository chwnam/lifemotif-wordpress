<?php

class LM_Singleton {

	protected function __construct() {

	}

	private function __clone() {

	}

	private function __wakeup() {

	}

	public static function get_instance() {

		static $instance = NULL;

		if ( NULL === $instance ) {
			$instance = new static();
		}

		return $instance;
	}
}