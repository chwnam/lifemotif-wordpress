<?php

class LM_Diary_Importer {

	private $diem;

	public function __construct() {

		$this->diem = LM_Diem_Wrapper_Factory::get_diem_wrapper();
	}

	public function import( $post_id, $force_update = FALSE ) {

		$post = WP_Post::get_instance( $post_id );

		if ( ! $post ) {
			return FALSE;
		}

		if( $post->post_status == 'publish' && ! $force_update ) {
			return $post_id;
		}

		// extract mid from the post
		$post_name_fields = explode( '-', $post->post_name );
		$mid              = $post_name_fields[0];
		// $tid           = $post_name_fields[1];
		$diary_date = "{$post_name_fields[2]}-{$post_name_fields[3]}-{$post_name_fields[4]}";

		// diem extract
		$extracted = json_decode( $this->diem->export( $mid ), TRUE );

		if ( is_array( $extracted ) ) {

			// confirm diary-date
			if ( $extracted['diary-date'] != $diary_date ) {
				throw new Exception( 'diary date mismatch!' );
			}

			$args = array(
				'ID'          => $post_id,
				'post_status' => 'publish',
			);

			$email_date     = new DateTime( $extracted['email-date'] );
			$email_date_gmt = clone $email_date;
			$email_date_gmt->setTimezone( new DateTimeZone( 'UTC' ) );

			$args['post_modified']     = $email_date->format( 'Y-m-d H:i:s' );
			$args['post_modified_gmt'] = $email_date_gmt->format( 'Y-m-d H:i:s' );

			// content
			$args['post_content'] = $extracted['content'];

			// attachments?

			// extract attachments

			// modify post status
			return wp_update_post( $args );
		}

		return FALSE;
	}
}