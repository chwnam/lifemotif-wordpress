<?php


class LM_Post_Mapper {

	public function do_mapping( $latest_mid ) {

		$handler = LM_Local_Database::get_handler();

		if ( ! $handler ) {
			error_log( __METHOD__ . ': stopping due to an invalid handler.' );

			return;
		}

		$query = "
			SELECT
				id_index.mid AS mid,
				id_index.tid AS tid,
				date_index.diary_date AS diary_date
			FROM
				diem_id_index AS id_index
			INNER JOIN diem_date_index AS date_index
				ON id_index.tid = date_index.tid
			WHERE
				id_index.mid > :mid			
		";

		$statement = $handler->prepare( $query );
		$statement->bindValue( ':mid', $latest_mid );

		$result = $statement->execute();

		$created = 0;
		$skipped = 0;

		$last_mid = $latest_mid;

		while ( $row = $result->fetchArray( SQLITE3_ASSOC ) ) {

			$mid = intval( $row['mid'] );
			$last_mid = $mid;

			if ( ! $mid ) {
				error_log( __METHOD__ . ": \$mid value '$mid' is invalid. " );
				continue;
			}

			$tid = intval( $row['tid'] );

			if ( ! $tid ) {
				error_log( __METHOD__ . ": \$tid value '$tid' is invalid. " );
				continue;
			}

			$diary_date = $row['diary_date'];

			if ( ! preg_match( '/\d{4}-\d{2}-\d{2}/', $diary_date ) ) {
				error_log( __METHOD__ . ": \$diary_date value '$diary_date' is invalid. " );
				continue;
			}

			$post_name = static::get_post_name( $mid, $tid, $diary_date );

			$post = get_page_by_path( $post_name, OBJECT, 'lifemotif-article' );

			if ( ! $post ) {
				// create post. Just create an empty post.
				$this->create_empty_mapped_post( $mid, $tid, $diary_date);
				++ $created;
			} else {
				// just skip
				++ $skipped;
			}
		}

		error_log( __METHOD__ . " finished. $created post(s) created, $skipped mid(s) skipped." );

		return array(
			'last_mid' => $last_mid,
			'created'  => $created,
			'skipped'  => $skipped,
		);
	}

	public static function get_post_name( $mid, $tid, $diary_date ) {

		return '0x' . dechex( $mid ) . '-' . '0x' . dechex( $tid ) . '-' . $diary_date;
	}

	private function create_empty_mapped_post( $mid, $tid, $diary_date ) {

		$result = wp_insert_post(
			array(
				'post_date'      => "$diary_date 00:00:00",
				'post_title'     => $diary_date,
				'post_name'      => static::get_post_name( $mid, $tid, $diary_date ),
				'post_status'    => 'draft',
				'post_type'      => 'lifemotif-article',
				'comment_status' => 'open',
				'ping_status'    => 'closed',
				'post_modified'  => '0000-00-00 00:00:00',
			)
		);

		if( is_wp_error( $result ) ) {
			error_log( __METHOD__ . ': creating a post failed. ' . $result->get_error_message() );
		}

		error_log( __METHOD__ . ": finished. $mid mapped to post ID $result" );
	}

	public function purge_all_posts() {

		$query = new WP_Query(
			array(
				'post_type' => 'lifemotif-article',
				'nopaging'  => true,
				'fields'    => 'ids',
			)
		);

		foreach( $query->posts as $post ) {
			wp_delete_post( $post, true );
		}
	}
}
