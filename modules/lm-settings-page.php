<?php

function lm_init_settings() {

	register_setting( 'lm-options', 'lm-profile-path', '' );
	register_setting( 'lm-options', 'lm-log-level', '' );
	register_setting( 'lm-options', 'lm-log-file', '' );
	register_setting( 'lm-options', 'lm-diem-path', '' );
	register_setting( 'lm-options', 'lm-python-path', '' );
}

add_action( 'admin_init', 'lm_init_settings' );


function lm_output_settings_page() { ?>
	<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">
		<?php
		settings_fields( 'lm-options' );
		do_settings_sections( 'lm-settings-page' );
		submit_button();
		?>
	</form>
<?php }


function lm_init_settings_page() {

	add_submenu_page(
		'lm-admin-page',
		__( 'LifeMotif Settings', 'lmwp' ),
		__( 'Settings', 'lmwp' ),
		'manage_options',
		'lm-settings-page',
		'lm_output_settings_page'
	);
}

add_action( 'admin_menu', 'lm_init_settings_page' );


function lm_init_sections() {

	add_settings_section( 'lm-settings-section', __( 'LifeMotif Settings', 'lmwp' ), '', 'lm-settings-page' );
}

add_action( 'admin_init', 'lm_init_sections' );


function lm_output_profile_path( array $args ) {

	$id_name = 'lm-profile-path';
	$val     = esc_attr( get_option( $id_name ) );

	echo '<input type="text" class="input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
	echo '<span class="description">' . __( 'Diem\'s profile path', 'lmwp' ) . '</span>';
}


function lm_output_log_level( array $args ) {

	$id_name = 'lm-log-level';
	$val     = esc_attr( get_option( $id_name ) );

	$levels = array( 'CRITICAL', 'ERROR', 'WARNING', 'INFO', 'DEBUG' );

	echo "<select id=\"$id_name\" name=\"$id_name\">";
	foreach( $levels as $level ) {
		echo "<option value=\"$level\" " . selected( $val, $level, FALSE ) . ">$level</option>";
	}
	echo "</select>";

	echo '<span class="description">' . __( 'Diem\'s log level', 'lmwp' ) . '</span>';
}


function lm_output_log_file( array $args ) {

	$id_name = 'lm-log-file';
	$val     = esc_attr( get_option( $id_name ) );

	echo '<input type="text" class="input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
	echo '<span class="description">' . __( 'Diem\'s log file path', 'lmwp' ) . '</span>';
}


function lm_output_diem_path( array $args ) {

	$id_name = 'lm-diem-path';
	$val     = esc_attr( get_option( $id_name ) );

	echo '<input type="text" class="input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
	echo '<span class="description">' . __( 'Diem sciript\'s directory path', 'lmwp' ) . '</span>';
}


function lm_output_python_path( array $args ) {

	$id_name = 'lm-python-path';
	$val     = esc_attr( get_option( $id_name ) );

	echo '<input type="text" class="input" id="' . $id_name . '" name="' . $id_name . '" value="' . $val . '" />';
	echo '<span class="description">' . __( 'Python path', 'lmwp' ) . '</span>';
}


function lm_init_fields() {

	add_settings_field( 'profile-path', __( 'Profile Path', 'lmwp' ), 'lm_output_profile_path', 'lm-settings-page', 'lm-settings-section' );

	add_settings_field( 'log-level', __( 'Log Level', 'lmwp' ), 'lm_output_log_level', 'lm-settings-page', 'lm-settings-section' );

	add_settings_field( 'log-file', __( 'Log File', 'lmwp' ), 'lm_output_log_file', 'lm-settings-page', 'lm-settings-section' );

	add_settings_field( 'diem-path', __( 'Diem Path', 'lmwp' ), 'lm_output_diem_path', 'lm-settings-page', 'lm-settings-section' );

	add_settings_field( 'python-path', __( 'Python Path', 'lmwp' ), 'lm_output_python_path', 'lm-settings-page', 'lm-settings-section' );
}

add_action( 'admin_init', 'lm_init_fields' );
