<?php

add_action( 'admin_notices', 'lm_output_diem_db_path_not_found' );

function lm_output_diem_db_path_not_found() {
	echo '<div class="notice notice-error">';
	echo '<p>' . __( 'Diem database path not found!', 'lmwp' ) . '</p>';
	echo '</div>';
}
