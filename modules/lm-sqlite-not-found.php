<?php

add_action( 'admin_notices', 'lm_output_sqlite_not_found' );

function lm_output_sqlite_not_found() {
	echo '<div class="notice notice-error">';
	echo '<p>' . __( 'Cannot set up LifeMotif. SQLite3 PHP extension is not supported!', 'lmwp' ) . '</p>';
	echo '</div>';
}
