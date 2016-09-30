<?php

class LM_Diem_Wrapper {

	private $python_path = '';

	private $diem_path = '';

	private $profile_path = '';

	private $log_level = '';

	private $log_file = '';

	function __construct( $python_path, $diem_path, $profile_path, $log_level, $log_file ) {

		$this->python_path = $python_path;

		$this->diem_path = $diem_path;

		$this->profile_path = $profile_path;

		$this->log_level = $log_level;

		$this->log_file = $log_file;
	}

	private function check_paths() {

		if ( ! $this->python_path || ! file_exists( $this->python_path ) ) {
			return FALSE;
		}

		if ( ! $this->diem_path || ! file_exists( $this->diem_path ) ) {
			return FALSE;
		}

		if ( ! $this->profile_path || ! file_exists( $this->profile_path ) ) {
			return FALSE;
		}

		return TRUE;
	}

	public function fetch_incrementally() {

		if ( ! $this->check_paths() ) {
			return NULL;
		}

		$command = sprintf(
			'%s run.py --log-level %s --log-file %s fetch-incrementally --profile %s',
			$this->python_path,
			$this->log_level,
			$this->log_file,
			$this->profile_path
		);

		$descriptorspec = array(
			0 => array( 'pipe', 'r' ),
			1 => array( 'pipe', 'w' ),
			2 => array( 'pipe', 'w' ),
		);

		$handle = proc_open( $command, $descriptorspec, $pipes, $this->diem_path );
		$stderr = stream_get_contents( $pipes[2] );
		$return = proc_close( $handle );

		if( $return !== 0 ) {
			return NULL;
		}

		return $stderr;
	}

	public function export( $mid ) {

		if( ! $this->check_paths() ) {
			return NULL;
		}

		$command = sprintf(
			'%s run.py --log-level %s --log-file %s export --profile %s --mid %s',
			$this->python_path,
			$this->log_level,
			$this->log_file,
			$this->profile_path,
			$mid
		);

		$descriptorspec = array(
			0 => array( 'pipe', 'r' ),
			1 => array( 'pipe', 'w' ),
			2 => array( 'pipe', 'w' ),
		);

		$handle = proc_open( $command, $descriptorspec, $pipes, $this->diem_path );
		$stdout = stream_get_contents( $pipes[1] );
		$return = proc_close( $handle );

		if( $return !== 0 ) {
			return NULL;
		}

		return $stdout;
	}
}