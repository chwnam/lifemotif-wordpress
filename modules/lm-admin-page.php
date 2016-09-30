<?php

function lm_admin_menu() {

	add_menu_page(
		__( 'LifeMotif', 'lmwp' ),
		__( 'LifeMotif', 'lmwp' ),
		'manage_options',
		'lm-admin-page',
		'lm_output_admin_page'
	);

	add_submenu_page(
		'lm-admin-page',
		__( 'LifeMotif', 'lmwp' ),
		__( 'LifeMotif', 'lmwp' ),
		'manage_options',
		'lm-admin-page',
		'lm_output_admin_page'
	);
}

add_action( 'admin_menu', 'lm_admin_menu' );


function is_mapping_task() {
	return isset( $_GET['action'] ) && $_GET['action'] == 'mapping';
}


function lm_output_admin_page() {

	$profile = LM_Diem_Profile::get_profile();
	if ( ! $profile ) {
		return;
	}

	$context = array(
		// 프로파일 경로와 그 내용들 덤프
		'profile_path'          => LM_Diem_Profile::get_profile_path(),
		'credential'            => lm_get_from_assoc( $profile, 'credential' ),
		'database'              => lm_get_from_assoc( $profile, 'database' ),
		'storage'               => lm_get_from_assoc( $profile, 'storage' ),
		'email'                 => lm_get_from_assoc( $profile, 'email' ),
		'label_id'              => lm_get_from_assoc( $profile, 'label-id' ),
		'archive_path'          => lm_get_from_assoc( $profile, 'archive-path' ),
		'timezone'              => lm_get_from_assoc( $profile, 'timezone' ),

		// 시스템 상태. 인덱스 수, 아카이브 수, 포스트 매핑 수
		'index_count'           => '',
		'archive_count'         => '',
		'mappings'              => array( 'draft' => '', 'publish' => '', ),

		// 라디이렉트 지시
		'archive_redirect_url'  => $_SERVER['REQUEST_URI'],
		'mapping_redirect_url'  => $_SERVER['REQUEST_URI'],

		// 조작 후 결과 출력
		'archive_update_output' => '',
		'mapping_update_output' => '',
	);

	// SQLite3에 인덱스 수 질의
	$db = LM_Local_Database::get_handler();
	if ( $db ) {
		$query                  = 'SELECT COUNT(*) FROM diem_id_index';
		$context['index_count'] = $db->querySingle( $query );
	}

	// 아카이브 숫자 조회
	if ( $context['archive_path'] && file_exists( $context['archive_path'] ) ) {
		$fi                       = new FilesystemIterator( $context['archive_path'], FilesystemIterator::SKIP_DOTS );
		$ri                       = new RegexIterator( $fi, '/[0-9a-f]+\.gz$/' );
		$context['archive_count'] = iterator_count( $ri );
	}

	// 포스트 매핑 조회
	$counts              = wp_count_posts( 'lifemotif-article', 'readable' );
	$context['mappings'] = array(
		'draft'   => $counts->draft,
		'publish' => $counts->publish,
	);

	// 라다이렉트 경로
	$context['archive_redirect_url'] = add_query_arg( 'a', 1, $context['archive_redirect_url'] );
	$context['archive_redirect_url'] = remove_query_arg( 'm', $context['archive_redirect_url'] );

	$context['mapping_redirect_url'] = add_query_arg( 'm', 1, $context['mapping_redirect_url'] );
	$context['mapping_redirect_url'] = remove_query_arg( 'a', $context['mapping_redirect_url'] );

	// 조작 결과 파라미터 'a', 'm'
	$archive_updated = lm_GET( 'a', 'intval', 0 );
	$mapping_updated = lm_GET( 'm', 'intval', 0 );

	if ( $archive_updated ) {
		$context['archive_update_output'] = nl2br( get_option( 'lm-diem-last-output' ) );
	}

	if ( $mapping_updated ) {
		$context['mapping_update_output'] = get_option( 'lm-mapper-last-output', array() );
	}

	lm_get_template( 'admin-page-status.php', $context );
}
